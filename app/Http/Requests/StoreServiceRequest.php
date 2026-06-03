<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'provider_id' => 'required|exists:users,id',
            'vehicle_id'  => 'required|exists:vehicles,id',
            'service_id'  => 'required|exists:services,id',
            'latitude'    => 'required|numeric|between:-90,90',
            'longitude'   => 'required|numeric|between:-180,180',
            'description' => 'nullable|string|min:10',
            'request_type' => 'required|in:immediate,scheduled',
            'requestTiming' => 'required|in:immediate,scheduled',
            'scheduled_date' => 'required_if:requestTiming,scheduled|date|after_or_equal:today',
            'scheduled_starts_at' => 'required_if:requestTiming,scheduled|date_format:H:i',
            'scheduled_ends_at' => 'required_if:requestTiming,scheduled|date_format:H:i|after:scheduled_starts_at',
        ];
    }
}
