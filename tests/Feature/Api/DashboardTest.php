<?php

namespace Tests\Feature\Api;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_endpoint_returns_summary_data(): void
    {
        $user = User::factory()->withWalletBalance(100000)->create([
            'name' => 'User A',
            'email' => 'usera@example.com',
        ]);
        $receiver = User::factory()->withWalletBalance(100000)->create([
            'name' => 'User B',
            'email' => 'userb@example.com',
        ]);

        Transaction::create([
            'transaction_code' => 'TRX-20260606010101-ABC123',
            'sender_wallet_id' => $user->wallet->id,
            'receiver_wallet_id' => $receiver->wallet->id,
            'amount' => 15000,
            'type' => 'transfer',
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/dashboard')
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Beranda berhasil dimuat.',
                'data' => [
                    'user' => [
                        'name' => 'User A',
                        'email' => 'usera@example.com',
                        'wallet' => [
                            'balance' => 100000,
                        ],
                    ],
                ],
            ])
            ->assertJsonPath('data.recent_transactions.0.transaction_code', 'TRX-20260606010101-ABC123')
            ->assertJsonPath('data.recent_transactions.0.counterpart_name', 'User B');
    }

    public function test_dashboard_endpoint_requires_authentication(): void
    {
        $this->getJson('/api/dashboard')
            ->assertUnauthorized();
    }
}
