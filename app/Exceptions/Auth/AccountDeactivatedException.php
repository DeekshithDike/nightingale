<?php

namespace App\Exceptions\Auth;

use Exception;

class AccountDeactivatedException extends Exception
{
    public function __construct(string $message = 'Account is deactivated')
    {
        parent::__construct($message, 401);
    }
} 