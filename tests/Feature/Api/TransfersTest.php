<?php

namespace Tests\Feature\Api;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransfersTest extends TestCase
{
    use RefreshDatabase;

    public function test_transfer_endpoint_returns_consistent_success_response(): void
    {
        $sender = User::factory()->withWalletBalance(100000)->create();
        $receiver = User::factory()->withWalletBalance(50000)->create();

        Sanctum::actingAs($sender);

        $this->postJson('/api/transfers', [
            'receiver_id' => $receiver->id,
            'amount' => 25000,
        ])
            ->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'Transfer berhasil.',
            ])
            ->assertJsonPath('data.transaction.amount', 25000)
            ->assertJsonPath('data.sender_wallet_balance', 75000)
            ->assertJsonPath('data.receiver_wallet_balance', 75000);

        $this->assertDatabaseCount('transactions', 1);

        $transaction = Transaction::query()->first();
        $this->assertSame('transfer', $transaction->type);
    }

    public function test_transfer_endpoint_returns_error_for_insufficient_balance(): void
    {
        $sender = User::factory()->withWalletBalance(1000)->create();
        $receiver = User::factory()->withWalletBalance(50000)->create();

        Sanctum::actingAs($sender);

        $this->postJson('/api/transfers', [
            'receiver_id' => $receiver->id,
            'amount' => 5000,
        ])
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Saldo tidak mencukupi.',
            ]);

        $this->assertDatabaseCount('transactions', 0);
        $this->assertSame(1000, $sender->fresh()->wallet->balance);
        $this->assertSame(50000, $receiver->fresh()->wallet->balance);
    }

    public function test_transfer_endpoint_returns_error_for_invalid_amount(): void
    {
        $sender = User::factory()->withWalletBalance(100000)->create();
        $receiver = User::factory()->withWalletBalance(50000)->create();

        Sanctum::actingAs($sender);

        $this->postJson('/api/transfers', [
            'receiver_id' => $receiver->id,
            'amount' => 0,
        ])
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Nominal harus lebih besar dari 0.',
            ]);

        $this->assertDatabaseCount('transactions', 0);
        $this->assertSame(100000, $sender->fresh()->wallet->balance);
        $this->assertSame(50000, $receiver->fresh()->wallet->balance);
    }

    public function test_transfer_endpoint_returns_error_for_self_transfer(): void
    {
        $user = User::factory()->withWalletBalance(100000)->create();

        Sanctum::actingAs($user);

        $this->postJson('/api/transfers', [
            'receiver_id' => $user->id,
            'amount' => 1000,
        ])
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Anda tidak dapat mentransfer ke akun sendiri.',
            ]);

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_transfer_endpoint_requires_authentication(): void
    {
        $this->postJson('/api/transfers', [
            'receiver_id' => 1,
            'amount' => 1000,
        ])->assertUnauthorized();
    }
}
