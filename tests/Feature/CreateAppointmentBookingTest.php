<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateAppointmentBookingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cannot_create_appointment_booking_without_available_slot_id()
    {
        $user = \App\Models\User::factory()->create();
        \App\Models\Patient::factory()->create(['user_id' => $user->id]);
        \Laravel\Sanctum\Sanctum::actingAs($user, ['*']);
        $payload = [
            // 'available_slot_id' is missing
        ];
        $response = $this->postJson("/api/patients/appointments", $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('available_slot_id');
    }
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_create_appointment_booking()
    {
        $user = \App\Models\User::factory()->create();
        \App\Models\Patient::factory()->create(['user_id' => $user->id]);
        $doctor = \App\Models\Doctor::factory()->create();
        $slot = \App\Models\AvailableSlot::factory()->create([
            'doctor_id' => $doctor->id,
        ]);
        \Laravel\Sanctum\Sanctum::actingAs($user, ['*']);
        $payload = [
            'available_slot_id' => $slot->id,
            // Add other required fields if needed
        ];
        $response = $this->postJson("/api/patients/appointments", $payload);
        $response->assertStatus(201);
    }

    /** @test */
    public function unauthenticated_user_cannot_create_appointment_booking()
    {
        $doctor = \App\Models\Doctor::factory()->create();
        $slot = \App\Models\AvailableSlot::factory()->create([
            'doctor_id' => $doctor->id,
        ]);
        $payload = [
            'available_slot_id' => $slot->id,
        ];
        $response = $this->postJson("/api/patients/appointments", $payload);
        $response->assertStatus(401);
    }
}
