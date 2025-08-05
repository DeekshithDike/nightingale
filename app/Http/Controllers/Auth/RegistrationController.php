<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Services\RegistrationServiceInterface;
use App\Exceptions\Registration\RegistrationFailedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\DoctorRegistrationRequest;
use App\Http\Requests\Auth\PatientRegistrationRequest;
use Illuminate\Http\JsonResponse;

class RegistrationController extends Controller
{
    public function __construct(
        private RegistrationServiceInterface $registrationService
    ) {}

    /**
     * Register a new patient.
     *
     * @param PatientRegistrationRequest $request
     * @return JsonResponse
     */
    public function registerPatient(PatientRegistrationRequest $request): JsonResponse
    {
        try {
            $data = $this->registrationService->registerPatient($request->validated());

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Patient registered successfully',
            ], 201);

        } catch (RegistrationFailedException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Register a new doctor.
     *
     * @param DoctorRegistrationRequest $request
     * @return JsonResponse
     */
    public function registerDoctor(DoctorRegistrationRequest $request): JsonResponse
    {
        try {
            $data = $this->registrationService->registerDoctor($request->validated());

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Doctor registered successfully',
            ], 201);

        } catch (RegistrationFailedException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
} 