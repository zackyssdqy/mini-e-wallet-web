<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_transfer_form(): void
    {
        $user = User::factory()->withWalletBalance(100000)->create();
        $otherUser = User::factory()->withWalletBalance(50000)->create([
            'name' => 'Receiver',
            'email' => 'receiver@example.com',
        ]);

        $this->actingAs($user)
            ->get(route('transfers.create'))
            ->assertOk()
            ->assertInertia(function (Assert $page) use ($otherUser) {
                $page->component('Transfers/Create')
                    ->where('wallet.balance', 100000)
                    ->has('users', 1)
                    ->where('users.0.id', $otherUser->id)
                    ->where('users.0.name', 'Receiver')
                    ->where('users.0.email', 'receiver@example.com')
                    ->etc();
            });
    }

    public function test_user_can_transfer_funds_successfully(): void
    {
        $sender = User::factory()->withWalletBalance(100000)->create();
        $receiver = User::factory()->withWalletBalance(50000)->create();

        $response = $this
            ->actingAs($sender)
            ->from(route('dashboard'))
            ->post(route('transfers.store'), [
                'receiver_id' => $receiver->id,
                'amount' => 25000,
            ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Transfer berhasil.');

        $this->assertDatabaseHas('wallets', [
            'id' => $sender->wallet->id,
            'balance' => 75000,
        ]);

        $this->assertDatabaseHas('wallets', [
            'id' => $receiver->wallet->id,
            'balance' => 75000,
        ]);

        $this->assertDatabaseCount('transactions', 1);

        $transaction = Transaction::query()->first();

        $this->assertSame('transfer', $transaction->type);
        $this->assertSame(25000, $transaction->amount);
        $this->assertSame($sender->wallet->id, $transaction->sender_wallet_id);
        $this->assertSame($receiver->wallet->id, $transaction->receiver_wallet_id);
        $this->assertNotEmpty($transaction->transaction_code);
    }

    public function test_user_cannot_transfer_to_self(): void
    {
        $user = User::factory()->withWalletBalance(100000)->create();

        $response = $this
            ->actingAs($user)
            ->from(route('dashboard'))
            ->post(route('transfers.store'), [
                'receiver_id' => $user->id,
                'amount' => 1000,
            ]);

        $response->assertSessionHasErrors('receiver_id');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertSame(100000, $user->fresh()->wallet->balance);
    }

    public function test_user_cannot_transfer_more_than_balance(): void
    {
        $sender = User::factory()->withWalletBalance(1000)->create();
        $receiver = User::factory()->withWalletBalance(50000)->create();

        $response = $this
            ->actingAs($sender)
            ->from(route('dashboard'))
            ->post(route('transfers.store'), [
                'receiver_id' => $receiver->id,
                'amount' => 5000,
            ]);

        $response->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertSame(1000, $sender->fresh()->wallet->balance);
        $this->assertSame(50000, $receiver->fresh()->wallet->balance);
    }
}
