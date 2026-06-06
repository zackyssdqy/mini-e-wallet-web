<?php

namespace Tests\Feature\Api;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_transactions_endpoint_returns_paginated_history(): void
    {
        $user = User::factory()->withWalletBalance(100000)->create();
        $receiver = User::factory()->withWalletBalance(100000)->create();

        Transaction::create([
            'transaction_code' => 'TRX-20260606010101-ABC123',
            'sender_wallet_id' => $user->wallet->id,
            'receiver_wallet_id' => $receiver->wallet->id,
            'amount' => 15000,
            'type' => 'transfer',
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/transactions')
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Riwayat transaksi berhasil dimuat.',
                'data' => [
                    'pagination' => [
                        'current_page' => 1,
                        'sort' => 'desc',
                    ],
                ],
            ])
            ->assertJsonPath('data.transactions.0.transaction_code', 'TRX-20260606010101-ABC123')
            ->assertJsonPath('data.transactions.0.counterpart_name', $receiver->name);
    }

    public function test_transactions_endpoint_supports_sorting_ascending(): void
    {
        $user = User::factory()->withWalletBalance(100000)->create();
        $receiver = User::factory()->withWalletBalance(100000)->create();

        $older = Transaction::create([
            'transaction_code' => 'TRX-20260606010101-OLD123',
            'sender_wallet_id' => $user->wallet->id,
            'receiver_wallet_id' => $receiver->wallet->id,
            'amount' => 1000,
            'type' => 'transfer',
        ]);
        $older->timestamps = false;
        $older->created_at = now()->subDay();
        $older->updated_at = now()->subDay();
        $older->save();

        $newer = Transaction::create([
            'transaction_code' => 'TRX-20260606010101-NEW123',
            'sender_wallet_id' => $user->wallet->id,
            'receiver_wallet_id' => $receiver->wallet->id,
            'amount' => 2000,
            'type' => 'transfer',
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/transactions?sort=asc')
            ->assertOk()
            ->assertJsonPath('data.pagination.sort', 'asc')
            ->assertJsonPath('data.transactions.0.transaction_code', $older->transaction_code)
            ->assertJsonPath('data.transactions.1.transaction_code', $newer->transaction_code);
    }

    public function test_transactions_endpoint_requires_authentication(): void
    {
        $this->getJson('/api/transactions')
            ->assertUnauthorized();
    }
}
