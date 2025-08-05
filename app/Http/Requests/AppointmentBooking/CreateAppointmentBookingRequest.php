<?php

namespace App\Http\Requests\AppointmentBooking;

use Illuminate\Foundation\Http\FormRequest;

class CreateAppointmentBookingRequest extends FormRequest
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
            'available_slot_id' => ['required', 'string', 'uuid', 'exists:available_slots,id'],
            'status' => ['sometimes', 'string', 'in:pending,confirmed,cancelled,completed'],
            'notes' => ['sometimes', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'available_slot_id.required' => 'Available slot ID is required.',
            'available_slot_id.string' => 'Available slot ID must be a string.',
            'available_slot_id.uuid' => 'Available slot ID must be a valid UUID.',
            'available_slot_id.exists' => 'Selected slot does not exist.',
            'status.string' => 'Status must be a string.',
            'status.in' => 'Status must be one of: pending, confirmed, cancelled, completed.',
            'notes.string' => 'Notes must be a string.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }
} 