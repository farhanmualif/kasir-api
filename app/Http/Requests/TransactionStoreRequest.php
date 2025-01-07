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
            'transaction.cash' => 'required|numeric|min:0',
            'transaction.items' => 'required|array|min:1',
            'transaction.items.*.id_product' => 'required|integer|exists:products,id',
            'transaction.items.*.quantity' => 'required|integer|min:1',
            'transaction.discount_uuid' => 'nullable|string|exists:discounts,uuid', // Optional discount
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
            'transaction.cash.required' => 'Jumlah uang tunai wajib diisi.',
            'transaction.cash.numeric' => 'Jumlah uang tunai harus berupa angka.',
            'transaction.cash.min' => 'Jumlah uang tunai tidak boleh kurang dari 0.',

            'transaction.items.required' => 'Daftar produk wajib diisi.',
            'transaction.items.array' => 'Daftar produk harus berupa array.',
            'transaction.items.min' => 'Minimal harus ada satu produk dalam transaksi.',

            'transaction.items.*.id_product.required' => 'ID produk wajib diisi.',
            'transaction.items.*.id_product.integer' => 'ID produk harus berupa angka.',
            'transaction.items.*.id_product.exists' => 'Produk tidak ditemukan.',

            'transaction.items.*.quantity.required' => 'Jumlah produk wajib diisi.',
            'transaction.items.*.quantity.integer' => 'Jumlah produk harus berupa angka.',
            'transaction.items.*.quantity.min' => 'Jumlah produk minimal 1.',

            'transaction.discount_uuid.exists' => 'Diskon tidak valid.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            responseJson('Validasi gagal', $validator->errors(), false, 422)
        );
    }
}
