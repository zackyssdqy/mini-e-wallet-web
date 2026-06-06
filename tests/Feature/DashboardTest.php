<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_user_name_balance_and_recent_transactions(): void
    {
        $user = User::factory()->withWalletBalance(100000)->create([
            'name' => 'User A',
        ]);
        $receiver = User::factory()->withWalletBalance(100000)->create([
            'name' => 'User B',
        ]);

        Transaction::create([
            'transaction_code' => 'TRX-20260606010101-ABC123',
            'sender_wallet_id' => $user->wallet->id,
            'receiver_wallet_id' => $receiver->wallet->id,
            'amount' => 15000,
            'type' => 'transfer',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(function (Assert $page) {
                $page->component('Dashboard')
                    ->where('userName', 'User A')
                    ->where('wallet.balance', 100000)
                    ->has('recentTransactions', 1)
                    ->where('recentTransactions.0.type_label', 'Transfer keluar')
                    ->where('recentTransactions.0.counterpart_name', 'User B')
                    ->etc();
            });
    }
}
