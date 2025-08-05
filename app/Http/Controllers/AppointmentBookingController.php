<?php

namespace App\Http\Controllers;

use App\Contracts\Services\AppointmentBookingServiceInterface;
use App\Http\Requests\AppointmentBooking\CreateAppointmentBookingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentBookingController extends Controller
{
    public function __construct(
        private AppointmentBookingServiceInterface $appointmentBookingService
    ) {}

    /**
     * Get appointment bookings for a specific patient.
     *
     * @param Request $request
     * @param string $patientId
     * @return JsonResponse
     */
    public function getAppointmentsForPatient(Request $request, string $patientId): JsonResponse
    {
        if (!$this->appointmentBookingService->patientExists($patientId)) {
            return response()->json([
                'success' => false,
                'message' => 'Patient not found',
            ], 404);
        }

        $status = $request->query('status');
        $appointments = $this->appointmentBookingService->getAppointmentsForPatient($patientId, $status);

        return response()->json([
            'success' => true,
            'data' => $appointments,
            'message' => 'Appointment bookings retrieved successfully',
        ]);
    }

    /**
     * Get appointment bookings for a specific doctor.
     *
     * @param Request $request
     * @param string $doctorId
     * @return JsonResponse
     */
    public function getAppointmentsForDoctor(Request $request, string $doctorId): JsonResponse
    {
        if (!$this->appointmentBookingService->doctorExists($doctorId)) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found',
            ], 404);
        }

        $appointments = $this->appointmentBookingService->getAppointmentsForDoctor($doctorId);

        return response()->json([
            'success' => true,
            'data' => $appointments,
            'message' => 'Appointment bookings retrieved successfully',
        ]);
    }

    /**
     * Get all appointment bookings.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllAppointments(Request $request): JsonResponse
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        
        $appointments = $this->appointmentBookingService->getAllAppointments($startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $appointments,
            'message' => 'Appointment bookings retrieved successfully',
        ]);
    }

    /**
     * Create a new appointment booking for the authenticated patient.
     *
     * @param CreateAppointmentBookingRequest $request
     * @return JsonResponse
     */
    public function createAppointmentBooking(CreateAppointmentBookingRequest $request): JsonResponse
    {
        $user = $request->user();
        $patient = $user->patient ?? null;
        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => 'Authenticated user is not a patient',
            ], 403);
        }

        $availableSlotId = $request->validated()['available_slot_id'];

        if (!$this->appointmentBookingService->slotExistsAndAvailable($availableSlotId)) {
            return response()->json([
                'success' => false,
                'message' => 'Selected slot is not available',
            ], 422);
        }

        $booking = $this->appointmentBookingService->createAppointmentBooking(
            $patient->id,
            $availableSlotId,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'data' => $booking,
            'message' => 'Appointment booking created successfully',
        ], 201);
    }
}