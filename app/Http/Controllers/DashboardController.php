<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user()->loadMissing('wallet');
        $walletId = $user->wallet->id;

        $recentTransactions = Transaction::query()
            ->where(function ($query) use ($walletId) {
                $query->where('sender_wallet_id', $walletId)
                    ->orWhere('receiver_wallet_id', $walletId);
            })
            ->with([
                'senderWallet.user:id,name',
                'receiverWallet.user:id,name',
            ])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function (Transaction $transaction) use ($walletId) {
                $transaction->type_label = $transaction->sender_wallet_id === $walletId
                    ? 'Transfer keluar'
                    : 'Transfer masuk';

                $transaction->counterpart_name = $transaction->sender_wallet_id === $walletId
                    ? $transaction->receiverWallet->user->name
                    : $transaction->senderWallet->user->name;

                return $transaction;
            });

        return Inertia::render('Dashboard', [
            'userName' => $user->name,
            'wallet' => $user->wallet,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
