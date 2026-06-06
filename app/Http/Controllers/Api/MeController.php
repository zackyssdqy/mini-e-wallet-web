<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends ApiController
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing('wallet');

        return $this->success('Profil berhasil dimuat.', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'wallet' => [
                'id' => $user->wallet->id,
                'balance' => $user->wallet->balance,
            ],
        ]);
    }
}
