<?php

namespace App\Exceptions;

use App\Exceptions\Auth\AccountDeactivatedException;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Exceptions\Registration\RegistrationFailedException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (InvalidCredentialsException $e, Request $request) {
            if ($request->expectsJson()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 401);
            }
        });

        $this->renderable(function (AccountDeactivatedException $e, Request $request) {
            if ($request->expectsJson()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 401);
            }
        });

        $this->renderable(function (RegistrationFailedException $e, Request $request) {
            if ($request->expectsJson()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
        });

        $this->renderable(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
        });
    }
} 