<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetDoctorAppointmentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_get_doctor_appointments()
    {
        $user = \App\Models\User::factory()->create();
        $doctor = \App\Models\Doctor::factory()->create();
        \Laravel\Sanctum\Sanctum::actingAs($user, ['*']);
        $response = $this->getJson("/api/doctors/{$doctor->id}/appointments");
        $response->assertStatus(200);
    }

    /** @test */
    public function returns_404_for_invalid_doctor_id()
    {
        $user = \App\Models\User::factory()->create();
        \Laravel\Sanctum\Sanctum::actingAs($user, ['*']);
        $invalidDoctorId = 'non-existent-id';
        $response = $this->getJson("/api/doctors/{$invalidDoctorId}/appointments");
        $response->assertStatus(404);
    }
}
