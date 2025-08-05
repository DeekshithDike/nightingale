<?php

namespace App\Exceptions\Registration;

use Exception;

class RegistrationFailedException extends Exception
{
    public function __construct(string $message = 'Registration failed')
    {
        parent::__construct($message, 422);
    }
} 