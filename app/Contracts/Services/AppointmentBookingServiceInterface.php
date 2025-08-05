<?php

namespace App\Contracts\Services;

interface AppointmentBookingServiceInterface
{
    /**
     * Get appointment bookings for a specific patient.
     *
     * @param string $patientId
     * @param string|null $status
     * @return array
     */
    public function getAppointmentsForPatient(string $patientId, ?string $status = null): array;

    /**
     * Get appointment bookings for a specific doctor.
     *
     * @param string $doctorId
     * @return array
     */
    public function getAppointmentsForDoctor(string $doctorId): array;

    /**
     * Get all appointment bookings.
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getAllAppointments(?string $startDate = null, ?string $endDate = null): array;

    /**
     * Create a new appointment booking.
     *
     * @param string $patientId
     * @param string $availableSlotId
     * @param array $bookingData
     * @return array
     */
    public function createAppointmentBooking(string $patientId, string $availableSlotId, array $bookingData): array;

    /**
     * Check if a patient exists.
     *
     * @param string $patientId
     * @return bool
     */
    public function patientExists(string $patientId): bool;

    /**
     * Check if a doctor exists.
     *
     * @param string $doctorId
     * @return bool
     */
    public function doctorExists(string $doctorId): bool;

    /**
     * Check if an available slot exists and is available.
     *
     * @param string $slotId
     * @return bool
     */
    public function slotExistsAndAvailable(string $slotId): bool;
} 