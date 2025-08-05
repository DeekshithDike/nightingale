<?php

namespace App\Http\Requests\AvailableSlot;

use Illuminate\Foundation\Http\FormRequest;

class CreateAvailableSlotRequest extends FormRequest
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
            'slots' => ['required', 'array', 'min:1'],
            'slots.*.date' => ['required', 'date', 'after_or_equal:today'],
            'slots.*.start_time' => ['required', 'date_format:H:i'],
            'slots.*.end_time' => ['required', 'date_format:H:i', 'after:slots.*.start_time'],
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
            'slots.required' => 'Slots data is required.',
            'slots.array' => 'Slots must be an array.',
            'slots.min' => 'At least one slot must be provided.',
            'slots.*.date.required' => 'Date is required for each slot.',
            'slots.*.date.date' => 'Date must be a valid date.',
            'slots.*.date.after_or_equal' => 'Date must be today or in the future.',
            'slots.*.start_time.required' => 'Start time is required for each slot.',
            'slots.*.start_time.date_format' => 'Start time must be in HH:MM format.',
            'slots.*.end_time.required' => 'End time is required for each slot.',
            'slots.*.end_time.date_format' => 'End time must be in HH:MM format.',
            'slots.*.end_time.after' => 'End time must be after start time.',
        ];
    }
} 