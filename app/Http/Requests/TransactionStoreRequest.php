<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

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
            'transaction.items.*.item_price' => 'required|integer',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'transaction.cash' => 'Field cash wajib diisi dan harus berupa angka.',
            'transaction.items' => 'Field items wajib diisi dan harus berupa array.',
            'transaction.items.*.id_product' => 'Field ID produk wajib diisi dan harus berupa angka.',
            'transaction.items.*.name' => 'Field nama produk wajib diisi dan harus berupa string.',
            'transaction.items.*.quantity' => 'Field jumlah produk wajib diisi dan harus berupa angka.',
            'transaction.items.*.item_price' => 'Field harga produk wajib diisi dan harus berupa angka.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(\responseJson('Validation error',$validator->errors(),false,422));
    }
}
