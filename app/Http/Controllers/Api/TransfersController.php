<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TransferRequest;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class TransfersController extends ApiController
{
    public function store(TransferRequest $request, TransferService $transferService): JsonResponse
    {
        try {
            $data = $request->validated();
            $senderWallet = $request->user()->wallet()->firstOrFail();
            $receiverWallet = User::findOrFail($data['receiver_id'])->wallet()->firstOrFail();

            $transaction = $transferService->transfer(
                senderWallet: $senderWallet,
                receiverWallet: $receiverWallet,
                amount: (int) $data['amount'],
            );

            $transaction->load([
                'senderWallet.user:id,name',
                'receiverWallet.user:id,name',
            ]);

            return $this->success('Transfer berhasil.', [
                'transaction' => $this->formatTransaction($transaction, $senderWallet->id),
                'sender_wallet_balance' => $senderWallet->fresh()->balance,
                'receiver_wallet_balance' => $receiverWallet->fresh()->balance,
            ], 201);
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first() ?? 'Transfer gagal.';

            return $this->error($message, 422);
        }
    }

    private function formatTransaction(Transaction $transaction, int $walletId): array
    {
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
    }
}
