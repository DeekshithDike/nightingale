<?php

namespace App\Contracts\Services;

interface AvailableSlotServiceInterface
{
    /**
     * Get available slots for a specific doctor.
     *
     * @param string $doctorId
     * @param string|null $date
     * @return array
     */
    public function getAvailableSlotsForDoctor(string $doctorId, ?string $date = null): array;

    /**
     * Get all available slots.
     *
     * @param string|null $specialtyId
     * @return array
     */
    public function getAllAvailableSlots(?string $specialtyId = null): array;

    /**
     * Create available slots for a doctor.
     *
     * @param string $doctorId
     * @param array $slotsData
     * @return array
     */
    public function createAvailableSlots(string $doctorId, array $slotsData): array;

    /**
     * Check if a doctor exists.
     *
     * @param string $doctorId
     * @return bool
     */
    public function doctorExists(string $doctorId): bool;
} 