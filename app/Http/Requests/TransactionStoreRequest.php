<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionStoreRequest extends FormRequest
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
            'transaction.cash' => 'required|numeric',
            'transaction.items' => 'required|array',
            'transaction.items.*.id_product' => 'required|integer',
            'transaction.items.*.name' => 'required|string',
            'transaction.items.*.quantity' => 'required|integer',
            'transaction.items.*.total_price' => 'required|numeric',
        ];
    }
}
