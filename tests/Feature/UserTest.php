<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_get_their_user_info()
    {
        $user = \App\Models\User::factory()->create();
        \Laravel\Sanctum\Sanctum::actingAs($user, ['*']);
        $response = $this->getJson('/api/user');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            // Optionally check for user data structure
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_get_user_info()
    {
        $response = $this->getJson('/api/user');
        $response->assertStatus(401);
    }
}
