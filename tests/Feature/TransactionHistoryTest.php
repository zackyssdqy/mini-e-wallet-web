<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TransactionHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaction_history_page_is_displayed(): void
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

        $this->actingAs($user)
            ->get(route('transactions.index'))
            ->assertOk()
            ->assertInertia(function (Assert $page) use ($receiver) {
                $page->component('Transactions/Index')
                    ->where('sort', 'desc')
                    ->has('transactions.data', 1)
                    ->where('transactions.data.0.transaction_code', 'TRX-20260606010101-ABC123')
                    ->where('transactions.data.0.type_label', 'Transfer keluar')
                    ->where('transactions.data.0.counterpart_name', $receiver->name)
                    ->etc();
            });
    }

    public function test_transaction_history_is_sorted_by_date_desc_by_default(): void
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

        $response = $this->actingAs($user)->get(route('transactions.index'));

        $response->assertOk()
            ->assertInertia(function (Assert $page) use ($newer, $older) {
                $page->component('Transactions/Index')
                    ->where('sort', 'desc')
                    ->where('transactions.data.0.transaction_code', $newer->transaction_code)
                    ->where('transactions.data.1.transaction_code', $older->transaction_code)
                    ->etc();
            });
    }
}
