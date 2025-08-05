<?php

namespace App\Http\Controllers;

use App\Contracts\Services\AvailableSlotServiceInterface;
use App\Http\Requests\AvailableSlot\CreateAvailableSlotRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvailableSlotController extends Controller
{
    public function __construct(
        private AvailableSlotServiceInterface $availableSlotService
    ) {}

    /**
     * Get available slots for a specific doctor.
     *
     * @param Request $request
     * @param string $doctorId
     * @return JsonResponse
     */
    public function getAvailableSlotsForDoctor(Request $request, string $doctorId): JsonResponse
    {
        if (!$this->availableSlotService->doctorExists($doctorId)) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found',
            ], 404);
        }

        $date = $request->query('date');
        $slots = $this->availableSlotService->getAvailableSlotsForDoctor($doctorId, $date);

        return response()->json([
            'success' => true,
            'data' => $slots,
            'message' => 'Available slots retrieved successfully',
        ]);
    }

    /**
     * Get all available slots.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllAvailableSlots(Request $request): JsonResponse
    {
        $specialtyId = $request->query('specialty_id');
        $slots = $this->availableSlotService->getAllAvailableSlots($specialtyId);

        return response()->json([
            'success' => true,
            'data' => $slots,
            'message' => 'Available slots retrieved successfully',
        ]);
    }

    /**
     * Create available slots for a doctor.
     *
     * @param CreateAvailableSlotRequest $request
     * @param string $doctorId
     * @return JsonResponse
     */
    public function createAvailableSlots(CreateAvailableSlotRequest $request, string $doctorId): JsonResponse
    {
        if (!$this->availableSlotService->doctorExists($doctorId)) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found',
            ], 404);
        }

        $slots = $this->availableSlotService->createAvailableSlots($doctorId, $request->validated()['slots']);

        return response()->json([
            'success' => true,
            'data' => $slots,
            'message' => 'Available slots created successfully',
        ], 201);
    }
} 