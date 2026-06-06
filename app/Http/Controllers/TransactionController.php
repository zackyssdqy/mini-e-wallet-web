<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user()->loadMissing('wallet');
        $walletId = $user->wallet->id;
        $sortDirection = $request->string('sort')->lower()->toString() === 'asc' ? 'asc' : 'desc';

        $transactions = Transaction::query()
            ->where(function ($query) use ($walletId) {
                $query->where('sender_wallet_id', $walletId)
                    ->orWhere('receiver_wallet_id', $walletId);
            })
            ->with([
                'senderWallet.user:id,name',
                'receiverWallet.user:id,name',
            ])
            ->orderBy('created_at', $sortDirection)
            ->paginate(10)
            ->withQueryString();

        $transactions->getCollection()->transform(function (Transaction $transaction) use ($walletId) {
            $transaction->type_label = $transaction->sender_wallet_id === $walletId
                ? 'Transfer keluar'
                : 'Transfer masuk';

            $transaction->counterpart_name = $transaction->sender_wallet_id === $walletId
                ? $transaction->receiverWallet->user->name
                : $transaction->senderWallet->user->name;

            return $transaction;
        });

        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions,
            'sort' => $sortDirection,
        ]);
    }
}
