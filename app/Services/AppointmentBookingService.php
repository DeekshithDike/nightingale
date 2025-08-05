<?php

namespace App\Services;

use App\Contracts\Services\AppointmentBookingServiceInterface;
use App\Models\AppointmentBooking;
use App\Models\AvailableSlot;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;

class AppointmentBookingService implements AppointmentBookingServiceInterface
{
    /**
     * Get appointment bookings for a specific patient.
     *
     * @param string $patientId
     * @param string|null $status
     * @return array
     */
    public function getAppointmentsForPatient(string $patientId, ?string $status = null): array
    {
        $query = AppointmentBooking::with([
            'availableSlot.doctor.specialty',
            'patient.user'
        ])->forPatient($patientId);

        if ($status) {
            $query->byStatus($status);
        }

        return $query->get()->map(function ($booking) {
            return [
                'id' => $booking->id,
                'available_slot_id' => $booking->available_slot_id,
                'patient_id' => $booking->patient_id,
                'status' => $booking->status,
                'notes' => $booking->notes,
                'available_slot' => [
                    'id' => $booking->availableSlot->id,
                    'date' => $booking->availableSlot->date->format('Y-m-d'),
                    'start_time' => $booking->availableSlot->start_time->format('H:i'),
                    'end_time' => $booking->availableSlot->end_time->format('H:i'),
                    'doctor' => [
                        'id' => $booking->availableSlot->doctor->id,
                        'name' => $booking->availableSlot->doctor->name,
                        'specialty' => [
                            'id' => $booking->availableSlot->doctor->specialty->id,
                            'name' => $booking->availableSlot->doctor->specialty->name,
                        ],
                    ],
                ],
            ];
        })->toArray();
    }

    /**
     * Get appointment bookings for a specific doctor.
     *
     * @param string $doctorId
     * @return array
     */
    public function getAppointmentsForDoctor(string $doctorId): array
    {
        return AppointmentBooking::with([
            'availableSlot',
            'patient.user'
        ])->forDoctor($doctorId)->get()->map(function ($booking) {
            return [
                'id' => $booking->id,
                'available_slot_id' => $booking->available_slot_id,
                'patient_id' => $booking->patient_id,
                'status' => $booking->status,
                'notes' => $booking->notes,
                'available_slot' => [
                    'id' => $booking->availableSlot->id,
                    'date' => $booking->availableSlot->date->format('Y-m-d'),
                    'start_time' => $booking->availableSlot->start_time->format('H:i'),
                    'end_time' => $booking->availableSlot->end_time->format('H:i'),
                ],
                'patient' => [
                    'id' => $booking->patient->id,
                    'user' => [
                        'id' => $booking->patient->user->id,
                        'name' => $booking->patient->user->name,
                        'email' => $booking->patient->user->email,
                    ],
                ],
            ];
        })->toArray();
    }

    /**
     * Get all appointment bookings.
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getAllAppointments(?string $startDate = null, ?string $endDate = null): array
    {
        $query = AppointmentBooking::with([
            'availableSlot.doctor.specialty',
            'patient.user'
        ]);

        if ($startDate && $endDate) {
            $query->inDateRange($startDate, $endDate);
        }

        return $query->get()->map(function ($booking) {
            return [
                'id' => $booking->id,
                'available_slot_id' => $booking->available_slot_id,
                'patient_id' => $booking->patient_id,
                'status' => $booking->status,
                'notes' => $booking->notes,
                'available_slot' => [
                    'id' => $booking->availableSlot->id,
                    'date' => $booking->availableSlot->date->format('Y-m-d'),
                    'start_time' => $booking->availableSlot->start_time->format('H:i'),
                    'end_time' => $booking->availableSlot->end_time->format('H:i'),
                    'doctor' => [
                        'id' => $booking->availableSlot->doctor->id,
                        'name' => $booking->availableSlot->doctor->name,
                        'specialty' => [
                            'id' => $booking->availableSlot->doctor->specialty->id,
                            'name' => $booking->availableSlot->doctor->specialty->name,
                        ],
                    ],
                ],
                'patient' => [
                    'id' => $booking->patient->id,
                    'user' => [
                        'id' => $booking->patient->user->id,
                        'name' => $booking->patient->user->name,
                        'email' => $booking->patient->user->email,
                    ],
                ],
            ];
        })->toArray();
    }

    /**
     * Create a new appointment booking.
     *
     * @param string $patientId
     * @param string $availableSlotId
     * @param array $bookingData
     * @return array
     */
    public function createAppointmentBooking(string $patientId, string $availableSlotId, array $bookingData): array
    {
        return DB::transaction(function () use ($patientId, $availableSlotId, $bookingData) {
            // Mark the slot as booked
            $slot = AvailableSlot::findOrFail($availableSlotId);
            $slot->update(['is_booked' => true]);

            // Create the appointment booking
            $booking = AppointmentBooking::create([
                'available_slot_id' => $availableSlotId,
                'patient_id' => $patientId,
                'status' => $bookingData['status'] ?? 'pending',
                'notes' => $bookingData['notes'] ?? null,
            ]);

            // Load relationships for response
            $booking->load(['availableSlot.doctor.specialty', 'patient.user']);

            return [
                'id' => $booking->id,
                'available_slot_id' => $booking->available_slot_id,
                'patient_id' => $booking->patient_id,
                'status' => $booking->status,
                'notes' => $booking->notes,
                'available_slot' => [
                    'id' => $booking->availableSlot->id,
                    'date' => $booking->availableSlot->date->format('Y-m-d'),
                    'start_time' => $booking->availableSlot->start_time->format('H:i'),
                    'end_time' => $booking->availableSlot->end_time->format('H:i'),
                    'doctor' => [
                        'id' => $booking->availableSlot->doctor->id,
                        'name' => $booking->availableSlot->doctor->name,
                        'specialty' => [
                            'id' => $booking->availableSlot->doctor->specialty->id,
                            'name' => $booking->availableSlot->doctor->specialty->name,
                        ],
                    ],
                ],
                'patient' => [
                    'id' => $booking->patient->id,
                    'user' => [
                        'id' => $booking->patient->user->id,
                        'name' => $booking->patient->user->name,
                        'email' => $booking->patient->user->email,
                    ],
                ],
            ];
        });
    }

    /**
     * Check if a patient exists.
     *
     * @param string $patientId
     * @return bool
     */
    public function patientExists(string $patientId): bool
    {
        return Patient::where('id', $patientId)->exists();
    }

    /**
     * Check if a doctor exists.
     *
     * @param string $doctorId
     * @return bool
     */
    public function doctorExists(string $doctorId): bool
    {
        return Doctor::where('id', $doctorId)->exists();
    }

    /**
     * Check if an available slot exists and is available.
     *
     * @param string $slotId
     * @return bool
     */
    public function slotExistsAndAvailable(string $slotId): bool
    {
        return AvailableSlot::where('id', $slotId)
            ->where('is_booked', false)
            ->exists();
    }
} 