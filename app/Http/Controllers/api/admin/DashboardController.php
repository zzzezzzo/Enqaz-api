<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\ProviderProfile;
use App\Models\Rating;
use App\Models\RequestStatus;
use App\Models\Role;
use App\Models\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $todayStart = Carbon::today();
        $tomorrowStart = Carbon::tomorrow();
        $weekStart = Carbon::now()->startOfWeek();
        $nextWeekStart = Carbon::now()->copy()->addWeek()->startOfWeek();
        $lastWeekStart = Carbon::now()->copy()->subWeek()->startOfWeek();

        $totalRequestsToday = ServiceRequest::whereBetween('created_at', [$todayStart, $tomorrowStart])->count();
        $activeRequests = $this->countByStatuses(['accepted', 'in_progress']);
        $registeredDrivers = $this->countUsersByRole('provider');
        $registeredWorkshops = ProviderProfile::count();
        $activeWinches = $this->countActiveWinchProviders();

        $weeklyCards = [
            'total_requests_today' => $this->weeklyChangeData(
                ServiceRequest::whereBetween('created_at', [$weekStart, $nextWeekStart])->count(),
                ServiceRequest::whereBetween('created_at', [$lastWeekStart, $weekStart])->count()
            ),
            'active_requests' => $this->weeklyChangeData(
                $this->countByStatuses(['accepted', 'in_progress'], $weekStart, $nextWeekStart),
                $this->countByStatuses(['accepted', 'in_progress'], $lastWeekStart, $weekStart)
            ),
            'registered_drivers' => $this->weeklyChangeData(
                $this->countUsersByRole('provider', $weekStart, $nextWeekStart),
                $this->countUsersByRole('provider', $lastWeekStart, $weekStart)
            ),
            'registered_workshops' => $this->weeklyChangeData(
                ProviderProfile::whereBetween('created_at', [$weekStart, $nextWeekStart])->count(),
                ProviderProfile::whereBetween('created_at', [$lastWeekStart, $weekStart])->count()
            ),
            'active_winches' => $this->weeklyChangeData(
                $this->countActiveWinchProviders($weekStart, $nextWeekStart),
                $this->countActiveWinchProviders($lastWeekStart, $weekStart)
            ),
        ];

        $averageResponseTimeInMinutes = $this->averageResponseTimeInMinutes();
        $completionRate = $this->completionRate();
        $customerSatisfaction = (float) Rating::avg('rating');

        $recentActivity = ServiceRequest::with(['provider.providerProfile', 'service', 'status'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function (ServiceRequest $request): array {
                return [
                    'request_id' => $request->id,
                    'display_request_id' => 'REQ-' . str_pad((string) $request->id, 5, '0', STR_PAD_LEFT),
                    'driver' => $request->provider?->name,
                    'workshop' => $request->provider?->providerProfile?->workShopName,
                    'service' => $request->service?->name,
                    'status' => $request->status?->name,
                    'time_ago' => $request->created_at?->diffForHumans(),
                ];
            });

        return response()->json([
            'cards' => [
                'total_requests_today' => $totalRequestsToday,
                'active_requests' => $activeRequests,
                'registered_drivers' => $registeredDrivers,
                'registered_workshops' => $registeredWorkshops,
                'active_winches' => $activeWinches,
                'weekly_change' => $weeklyCards,
            ],
            'system_performance' => [
                'average_response_time_minutes' => round($averageResponseTimeInMinutes, 1),
                'completion_rate' => round($completionRate, 2),
                'customer_satisfaction' => round($customerSatisfaction, 2),
                'customer_satisfaction_scale' => 5,
            ],
            'pending_approvals' => [
                'new_workshops' => ProviderProfile::where('is_available', false)->count(),
                'winch_registrations' => $this->pendingWinchRegistrations(),
            ],
            'live_system_map' => [
                'active_requests' => $this->activeRequestMapPoints(),
                'workshops' => $this->workshopMapPoints(),
                'winches' => $this->winchMapPoints(),
            ],
            'recent_activity' => $recentActivity,
        ]);
    }

    private function countUsersByRole(string $roleName, ?Carbon $from = null, ?Carbon $to = null): int
    {
        $roleId = Role::where('name', $roleName)->value('id');

        if (! $roleId) {
            return 0;
        }

        $query = \App\Models\User::where('role_id', $roleId);

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        return $query->count();
    }

    private function countByStatuses(array $statuses, ?Carbon $from = null, ?Carbon $to = null): int
    {
        $statusIds = RequestStatus::whereIn('name', $statuses)->pluck('id');

        $query = ServiceRequest::whereIn('status_id', $statusIds);

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        return $query->count();
    }

    private function countActiveWinchProviders(?Carbon $from = null, ?Carbon $to = null): int
    {
        $query = ProviderProfile::query()
            ->where('is_available', true)
            ->whereHas('services', function ($serviceQuery): void {
                $serviceQuery->where('name', 'like', '%winch%');
            });

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        return $query->count();
    }

    private function pendingWinchRegistrations(): int
    {
        return ProviderProfile::query()
            ->where('is_available', false)
            ->whereHas('services', function ($serviceQuery): void {
                $serviceQuery->where('name', 'like', '%winch%');
            })
            ->count();
    }

    private function averageResponseTimeInMinutes(): float
    {
        $acceptedStatusId = RequestStatus::where('name', 'accepted')->value('id');
        $pendingStatusId = RequestStatus::where('name', 'pending')->value('id');

        if (! $acceptedStatusId || ! $pendingStatusId) {
            return 0;
        }

        $acceptedRequests = ServiceRequest::where('status_id', $acceptedStatusId)
            ->whereNotNull('created_at')
            ->get(['id', 'created_at', 'updated_at']);

        if ($acceptedRequests->isEmpty()) {
            return 0;
        }

        $minutes = $acceptedRequests->map(function (ServiceRequest $request): float {
            return (float) $request->created_at->diffInMinutes($request->updated_at);
        });

        return $minutes->avg() ?? 0;
    }

    private function completionRate(): float
    {
        $total = ServiceRequest::count();

        if ($total === 0) {
            return 0;
        }

        $completedStatusId = RequestStatus::where('name', 'completed')->value('id');

        if (! $completedStatusId) {
            return 0;
        }

        $completed = ServiceRequest::where('status_id', $completedStatusId)->count();

        return ($completed / $total) * 100;
    }

    private function weeklyChangeData(int $current, int $previous): array
    {
        if ($previous === 0) {
            return [
                'value' => $current,
                'change_percent' => $current > 0 ? 100.0 : 0.0,
                'direction' => $current > 0 ? 'up' : 'flat',
                'label' => 'vs last week',
            ];
        }

        $change = (($current - $previous) / $previous) * 100;
        $direction = $change > 0 ? 'up' : ($change < 0 ? 'down' : 'flat');

        return [
            'value' => $current,
            'change_percent' => round($change, 2),
            'direction' => $direction,
            'label' => 'vs last week',
        ];
    }

    private function activeRequestMapPoints(): Collection
    {
        $statusIds = RequestStatus::whereIn('name', ['accepted', 'in_progress'])->pluck('id');

        return ServiceRequest::whereIn('status_id', $statusIds)
            ->latest()
            ->limit(100)
            ->get(['id', 'latitude', 'longitude'])
            ->map(fn (ServiceRequest $request): array => [
                'id' => $request->id,
                'lat' => (float) $request->latitude,
                'lng' => (float) $request->longitude,
            ]);
    }

    private function workshopMapPoints(): Collection
    {
        return ProviderProfile::where('is_available', true)
            ->latest()
            ->limit(100)
            ->get(['id', 'workShopName', 'latitude', 'longitude'])
            ->map(fn (ProviderProfile $profile): array => [
                'id' => $profile->id,
                'name' => $profile->workShopName,
                'lat' => (float) $profile->latitude,
                'lng' => (float) $profile->longitude,
            ]);
    }

    private function winchMapPoints(): Collection
    {
        return ProviderProfile::where('is_available', true)
            ->whereHas('services', function ($serviceQuery): void {
                $serviceQuery->where('name', 'like', '%winch%');
            })
            ->latest()
            ->limit(100)
            ->get(['id', 'workShopName', 'latitude', 'longitude'])
            ->map(fn (ProviderProfile $profile): array => [
                'id' => $profile->id,
                'name' => $profile->workShopName,
                'lat' => (float) $profile->latitude,
                'lng' => (float) $profile->longitude,
            ]);
    }
}
