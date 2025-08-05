<?php

namespace App\Services;

use App\Contracts\Services\AvailableSlotServiceInterface;
use App\Models\AvailableSlot;
use App\Models\Doctor;
use Illuminate\Support\Facades\DB;

class AvailableSlotService implements AvailableSlotServiceInterface
{
    /**
     * Get available slots for a specific doctor.
     *
     * @param string $doctorId
     * @param string|null $date
     * @return array
     */
    public function getAvailableSlotsForDoctor(string $doctorId, ?string $date = null): array
    {
        $query = AvailableSlot::with(['doctor.specialty'])
            ->available()
            ->forDoctor($doctorId);

        if ($date) {
            $query->forDate($date);
        }

        return $query->get()->map(function ($slot) {
            return [
                'id' => $slot->id,
                'doctor_id' => $slot->doctor_id,
                'date' => $slot->date->format('Y-m-d'),
                'start_time' => $slot->start_time->format('H:i'),
                'end_time' => $slot->end_time->format('H:i'),
                'is_booked' => $slot->is_booked,
                'doctor' => [
                    'id' => $slot->doctor->id,
                    'name' => $slot->doctor->name,
                    'specialty' => [
                        'id' => $slot->doctor->specialty->id,
                        'name' => $slot->doctor->specialty->name,
                    ],
                ],
            ];
        })->toArray();
    }

    /**
     * Get all available slots.
     *
     * @param string|null $specialtyId
     * @return array
     */
    public function getAllAvailableSlots(?string $specialtyId = null): array
    {
        $query = AvailableSlot::with(['doctor.specialty'])
            ->available();

        if ($specialtyId) {
            $query->whereHas('doctor', function ($q) use ($specialtyId) {
                $q->where('specialty_id', $specialtyId);
            });
        }

        return $query->get()->map(function ($slot) {
            return [
                'id' => $slot->id,
                'doctor_id' => $slot->doctor_id,
                'date' => $slot->date->format('Y-m-d'),
                'start_time' => $slot->start_time->format('H:i'),
                'end_time' => $slot->end_time->format('H:i'),
                'is_booked' => $slot->is_booked,
                'doctor' => [
                    'id' => $slot->doctor->id,
                    'name' => $slot->doctor->name,
                    'specialty' => [
                        'id' => $slot->doctor->specialty->id,
                        'name' => $slot->doctor->specialty->name,
                    ],
                ],
            ];
        })->toArray();
    }

    /**
     * Create available slots for a doctor.
     *
     * @param string $doctorId
     * @param array $slotsData
     * @return array
     */
    public function createAvailableSlots(string $doctorId, array $slotsData): array
    {
        return DB::transaction(function () use ($doctorId, $slotsData) {
            $createdSlots = [];

            foreach ($slotsData as $slotData) {
                $slot = AvailableSlot::create([
                    'doctor_id' => $doctorId,
                    'date' => $slotData['date'],
                    'start_time' => $slotData['start_time'],
                    'end_time' => $slotData['end_time'],
                    'is_booked' => false,
                ]);

                $createdSlots[] = [
                    'id' => $slot->id,
                    'doctor_id' => $slot->doctor_id,
                    'date' => $slot->date->format('Y-m-d'),
                    'start_time' => $slot->start_time->format('H:i'),
                    'end_time' => $slot->end_time->format('H:i'),
                    'is_booked' => $slot->is_booked,
                ];
            }

            return $createdSlots;
        });
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
} 