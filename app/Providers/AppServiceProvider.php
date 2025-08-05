<?php

namespace App\Providers;

use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\AvailableSlotServiceInterface;
use App\Contracts\Services\AppointmentBookingServiceInterface;
use App\Contracts\Services\RegistrationServiceInterface;
use App\Services\AuthService;
use App\Services\AvailableSlotService;
use App\Services\AppointmentBookingService;
use App\Services\RegistrationService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(RegistrationServiceInterface::class, RegistrationService::class);
        $this->app->bind(AvailableSlotServiceInterface::class, AvailableSlotService::class);
        $this->app->bind(AppointmentBookingServiceInterface::class, AppointmentBookingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
