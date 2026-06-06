<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class TransferService
{
    public function transfer(Wallet $senderWallet, Wallet $receiverWallet, int $amount): Transaction
    {
        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Nominal harus lebih besar dari 0.',
            ]);
        }

        return DB::transaction(function () use ($senderWallet, $receiverWallet, $amount): Transaction {
            $lockedWallets = Wallet::query()
                ->whereIn('id', [$senderWallet->id, $receiverWallet->id])
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $lockedSenderWallet = $lockedWallets->get($senderWallet->id);
            $lockedReceiverWallet = $lockedWallets->get($receiverWallet->id);

            if (! $lockedSenderWallet || ! $lockedReceiverWallet) {
                throw ValidationException::withMessages([
                    'receiver_id' => 'Akun penerima tidak ditemukan.',
                ]);
            }

            if ($lockedSenderWallet->id === $lockedReceiverWallet->id) {
                throw ValidationException::withMessages([
                    'receiver_id' => 'Anda tidak dapat mentransfer ke akun sendiri.',
                ]);
            }

            if ($lockedSenderWallet->balance < $amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Saldo tidak mencukupi.',
                ]);
            }

            $lockedSenderWallet->balance -= $amount;
            $lockedSenderWallet->save();

            $lockedReceiverWallet->balance += $amount;
            $lockedReceiverWallet->save();

            return Transaction::create([
                'transaction_code' => $this->generateTransactionCode(),
                'sender_wallet_id' => $lockedSenderWallet->id,
                'receiver_wallet_id' => $lockedReceiverWallet->id,
                'amount' => $amount,
                'type' => 'transfer',
            ]);
        });
    }

    private function generateTransactionCode(): string
    {
        do {
            $code = 'TRX-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6));
        } while (Transaction::query()->where('transaction_code', $code)->exists());

        return $code;
    }
}
