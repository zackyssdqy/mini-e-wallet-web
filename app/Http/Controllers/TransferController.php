<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use App\Models\User;
use App\Services\TransferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransferController extends Controller
{
    public function create(Request $request): Response
    {
        $wallet = $request->user()->wallet()->firstOrFail();

        $users = User::query()
            ->whereKeyNot($request->user()->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return Inertia::render('Transfers/Create', [
            'users' => $users,
            'wallet' => $wallet,
        ]);
    }

    public function store(TransferRequest $request, TransferService $transferService): RedirectResponse
    {
        $data = $request->validated();

        $senderWallet = $request->user()->wallet()->firstOrFail();
        $receiverWallet = User::findOrFail($data['receiver_id'])->wallet()->firstOrFail();

        $transferService->transfer(
            senderWallet: $senderWallet,
            receiverWallet: $receiverWallet,
            amount: (int) $data['amount'],
        );

        return redirect()
            ->route('dashboard')
            ->with('success', 'Transfer berhasil.');
    }
}
