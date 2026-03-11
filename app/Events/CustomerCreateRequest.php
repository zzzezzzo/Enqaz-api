<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class CustomerCreateRequest implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public  $serviceRequest;
    public int $customerId;


    public function __construct( $serviceRequest, int $customerId)
    {
        $this->customerId= $customerId;
        $this->serviceRequest = $serviceRequest;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('provider.'.$this->serviceRequest->provider_id),
        ];
    }
    
    public function broadcastAs(): string
    {
        return 'customer-create-request';
    }
    public function broadcastWith(): array
    {
        return [
            'id' => $this->serviceRequest->id,
            'customer_id' => $this->customerId,
            'customer_name' => $this->serviceRequest->customer->name,
            'service' => $this->serviceRequest->service->name,
            'location' => [
                'latitude' => $this->serviceRequest->latitude,
                'longitude' => $this->serviceRequest->longitude,
            ],
            'description' => $this->serviceRequest->description,
        ];
    }
}
