<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionsController extends ApiController
{
    public function __invoke(Request $request): JsonResponse
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

        $items = $transactions->getCollection()->map(function (Transaction $transaction) use ($walletId) {
            return [
                'id' => $transaction->id,
                'transaction_code' => $transaction->transaction_code,
                'type' => $transaction->sender_wallet_id === $walletId ? 'transfer_out' : 'transfer_in',
                'amount' => $transaction->amount,
                'created_at' => $transaction->created_at?->toISOString(),
                'counterpart_name' => $transaction->sender_wallet_id === $walletId
                    ? $transaction->receiverWallet->user->name
                    : $transaction->senderWallet->user->name,
            ];
        })->values();

        return $this->success('Riwayat transaksi berhasil dimuat.', [
            'transactions' => $items,
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
                'sort' => $sortDirection,
            ],
        ]);
    }
}
