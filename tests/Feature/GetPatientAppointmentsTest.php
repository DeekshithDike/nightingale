<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetPatientAppointmentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_get_patient_appointments()
    {
        $user = \App\Models\User::factory()->create();
        $patient = \App\Models\Patient::factory()->create();
        \Laravel\Sanctum\Sanctum::actingAs($user, ['*']);
        $response = $this->getJson("/api/patients/{$patient->id}/appointments");
        $response->assertStatus(200);
    }

    /** @test */
    public function returns_404_for_invalid_patient_id()
    {
        $user = \App\Models\User::factory()->create();
        \Laravel\Sanctum\Sanctum::actingAs($user, ['*']);
        $invalidPatientId = 'non-existent-id';
        $response = $this->getJson("/api/patients/{$invalidPatientId}/appointments");
        $response->assertStatus(404);
    }
}
