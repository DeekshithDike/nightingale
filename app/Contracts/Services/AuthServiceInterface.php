<?php

namespace App\Contracts\Services;

use App\Models\User;

interface AuthServiceInterface
{
    /**
     * Authenticate a user with email and password.
     *
     * @param string $email
     * @param string $password
     * @return array
     * @throws \App\Exceptions\Auth\InvalidCredentialsException
     * @throws \App\Exceptions\Auth\AccountDeactivatedException
     */
    public function authenticate(string $email, string $password): array;

    /**
     * Create a token for the authenticated user.
     *
     * @param User $user
     * @return string
     */
    public function createToken(User $user): string;

    /**
     * Get user data with role-specific information.
     *
     * @param User $user
     * @return array
     */
    public function getUserData(User $user): array;
} 