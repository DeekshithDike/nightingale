<?php

namespace App\Services;

use App\Contracts\Services\AuthServiceInterface;
use App\Exceptions\Auth\AccountDeactivatedException;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    /**
     * Authenticate a user with email and password.
     *
     * @param string $email
     * @param string $password
     * @return array
     * @throws InvalidCredentialsException
     * @throws AccountDeactivatedException
     */
    public function authenticate(string $email, string $password): array
    {
        $user = User::with(['doctor.specialty', 'patient'])->where('email', $email)->first();

        if (!$user) {
            throw new InvalidCredentialsException();
        }

        if (!$user->isActive()) {
            throw new AccountDeactivatedException();
        }

        if (!Hash::check($password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        $token = $this->createToken($user);
        $userData = $this->getUserData($user);

        return [
            'user' => $userData,
            'token' => $token,
        ];
    }

    /**
     * Create a token for the authenticated user.
     *
     * @param User $user
     * @return string
     */
    public function createToken(User $user): string
    {
        return $user->createToken('auth-token')->plainTextToken;
    }

    /**
     * Get user data with role-specific information.
     *
     * @param User $user
     * @return array
     */
    public function getUserData(User $user): array
    {
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
        ];

        // Add role-specific data
        if ($user->isDoctor() && $user->doctor) {
            $userData['doctor'] = [
                'id' => $user->doctor->id,
                'specialty' => [
                    'id' => $user->doctor->specialty->id,
                    'name' => $user->doctor->specialty->name,
                ],
            ];
        }

        if ($user->isPatient() && $user->patient) {
            $userData['patient'] = [
                'id' => $user->patient->id,
                'dob' => $user->patient->dob?->format('Y-m-d'),
                'gender' => $user->patient->gender,
            ];
        }

        return $userData;
    }
} 