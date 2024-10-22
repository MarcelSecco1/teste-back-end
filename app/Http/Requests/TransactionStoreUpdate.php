<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionStoreUpdate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payer_id' => ['sometimes', 'integer', 'different:payee_id', 'exists:users,id'],
            'payee_id' => ['sometimes', 'integer', 'different:payer_id', 'exists:users,id'],
            'value' => ['sometimes', 'numeric', 'min:0.01'],
        ];
    }
}
