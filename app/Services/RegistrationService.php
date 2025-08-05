<?php

namespace App\Services;

use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\RegistrationServiceInterface;
use App\Exceptions\Registration\RegistrationFailedException;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegistrationService implements RegistrationServiceInterface
{
    public function __construct(
        private AuthServiceInterface $authService
    ) {}

    /**
     * Register a new patient.
     *
     * @param array $data
     * @return array
     * @throws RegistrationFailedException
     */
    public function registerPatient(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = $this->createUser($data, 'patient');
            $patient = $this->createPatient($user, $data);
            
            $token = $this->authService->createToken($user);
            $userData = $this->authService->getUserData($user);

            return [
                'user' => $userData,
                'token' => $token,
            ];
        });
    }

    /**
     * Register a new doctor.
     *
     * @param array $data
     * @return array
     * @throws RegistrationFailedException
     */
    public function registerDoctor(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = $this->createUser($data, 'doctor');
            $doctor = $this->createDoctor($user, $data);
            
            $token = $this->authService->createToken($user);
            $userData = $this->authService->getUserData($user);

            return [
                'user' => $userData,
                'token' => $token,
            ];
        });
    }

    /**
     * Create a user with the given data.
     *
     * @param array $data
     * @param string $role
     * @return User
     */
    public function createUser(array $data, string $role): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => $role,
            'is_active' => true,
        ]);
    }

    /**
     * Create a patient record for the user.
     *
     * @param User $user
     * @param array $data
     * @return Patient
     */
    public function createPatient(User $user, array $data): Patient
    {
        return Patient::create([
            'user_id' => $user->id,
            'dob' => $data['dob'],
            'gender' => $data['gender'],
        ]);
    }

    /**
     * Create a doctor record for the user.
     *
     * @param User $user
     * @param array $data
     * @return Doctor
     */
    public function createDoctor(User $user, array $data): Doctor
    {
        // Validate specialty exists
        $specialty = Specialty::findOrFail($data['specialty_id']);

        return Doctor::create([
            'name' => $data['name'],
            'user_id' => $user->id,
            'specialty_id' => $specialty->id,
        ]);
    }
} 