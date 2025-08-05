<?php

namespace App\Contracts\Services;

use App\Models\User;

interface RegistrationServiceInterface
{
    /**
     * Register a new patient.
     *
     * @param array $data
     * @return array
     * @throws \App\Exceptions\Registration\RegistrationFailedException
     */
    public function registerPatient(array $data): array;

    /**
     * Register a new doctor.
     *
     * @param array $data
     * @return array
     * @throws \App\Exceptions\Registration\RegistrationFailedException
     */
    public function registerDoctor(array $data): array;

    /**
     * Create a user with the given data.
     *
     * @param array $data
     * @param string $role
     * @return User
     */
    public function createUser(array $data, string $role): User;

    /**
     * Create a patient record for the user.
     *
     * @param User $user
     * @param array $data
     * @return \App\Models\Patient
     */
    public function createPatient(User $user, array $data): \App\Models\Patient;

    /**
     * Create a doctor record for the user.
     *
     * @param User $user
     * @param array $data
     * @return \App\Models\Doctor
     */
    public function createDoctor(User $user, array $data): \App\Models\Doctor;
} 