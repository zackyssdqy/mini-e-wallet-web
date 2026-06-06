<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MeTest extends TestCase
{
    use RefreshDatabase;

    public function test_me_endpoint_returns_authenticated_user_data(): void
    {
        $user = User::factory()->withWalletBalance(100000)->create([
            'name' => 'User A',
            'email' => 'usera@example.com',
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/me')
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Profil berhasil dimuat.',
                'data' => [
                    'id' => $user->id,
                    'name' => 'User A',
                    'email' => 'usera@example.com',
                    'wallet' => [
                        'balance' => 100000,
                    ],
                ],
            ]);
    }

    public function test_me_endpoint_requires_authentication(): void
    {
        $this->getJson('/api/me')
            ->assertUnauthorized();
    }
}
