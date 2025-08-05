<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\AvailableSlotController;
use App\Http\Controllers\AppointmentBookingController;

Route::get('/', function () {
    return response()->json(['message' => 'Welcome to the API!']);
});

// Public routes
Route::post('/login', [LoginController::class, 'login']);

// Registration routes
Route::prefix('register')->group(function () {
    Route::post('/patient', [RegistrationController::class, 'registerPatient']);
    Route::post('/doctor', [RegistrationController::class, 'registerDoctor']);
});

// Available slots routes
Route::get('/available-slots', [AvailableSlotController::class, 'getAllAvailableSlots']);
Route::get('/doctors/{doctor:uuid}/available-slots', [AvailableSlotController::class, 'getAvailableSlotsForDoctor']);

// Appointment bookings routes
Route::get('/appointment-bookings', [AppointmentBookingController::class, 'getAllAppointments']);
Route::get('/patients/{patient:uuid}/appointments', [AppointmentBookingController::class, 'getAppointmentsForPatient']);
Route::get('/doctors/{doctor:uuid}/appointments', [AppointmentBookingController::class, 'getAppointmentsForDoctor']);


// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'show']);
    Route::post('/logout', [LogoutController::class, 'logout']);

    // Doctor create slots
    Route::post('/doctors/{doctor:uuid}/available-slots', [AvailableSlotController::class, 'createAvailableSlots']);
    Route::post('/patients/appointments', [AppointmentBookingController::class, 'createAppointmentBooking']);
});
