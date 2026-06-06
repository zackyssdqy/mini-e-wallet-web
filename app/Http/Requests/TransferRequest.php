<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Validation\Rule;

class TransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'receiver_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
                Rule::notIn([$this->user()?->id]),
            ],
            'amount' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'receiver_id.not_in' => 'Anda tidak dapat mentransfer ke akun sendiri.',
            'amount.min' => 'Nominal harus lebih besar dari 0.',
        ];
    }

    protected function failedValidation(ValidatorContract $validator): void
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => collect($validator->errors()->all())->first() ?? 'Validasi gagal.',
            ], 422));
        }

        throw new \Illuminate\Validation\ValidationException($validator);
    }
}
