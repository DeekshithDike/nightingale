<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Services\AuthServiceInterface;
use App\Exceptions\Auth\AccountDeactivatedException;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __construct(
        private AuthServiceInterface $authService
    ) {}

    /**
     * Handle user login.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $this->ensureIsNotRateLimited($request);

        try {
            $data = $this->authService->authenticate(
                $request->email,
                $request->password
            );

            $this->clearRateLimit($request);

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Login successful',
            ], 200);

        } catch (InvalidCredentialsException $e) {
            $this->incrementRateLimit($request);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 401);

        } catch (AccountDeactivatedException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @param Request $request
     * @return void
     */
    private function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        // Return 429 status instead of throwing ValidationException
        abort(429, trans('auth.throttle', [
            'seconds' => $seconds,
            'minutes' => ceil($seconds / 60),
        ]));
    }

    /**
     * Increment the rate limit for the given request.
     *
     * @param Request $request
     * @return void
     */
    private function incrementRateLimit(Request $request): void
    {
        RateLimiter::hit($this->throttleKey($request));
    }

    /**
     * Clear the rate limit for the given request.
     *
     * @param Request $request
     * @return void
     */
    private function clearRateLimit(Request $request): void
    {
        RateLimiter::clear($this->throttleKey($request));
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param Request $request
     * @return string
     */
    private function throttleKey(Request $request): string
    {
        return mb_strtolower($request->input('email')).'|'.$request->ip();
    }
} 