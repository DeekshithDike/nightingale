<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetAllAppointmentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_get_all_appointments()
    {
        $user = \App\Models\User::factory()->create();
        \Laravel\Sanctum\Sanctum::actingAs($user, ['*']);
        $response = $this->getJson('/api/appointment-bookings');
        $response->assertStatus(200);
    }

    // Route is now public; unauthenticated test removed
}
