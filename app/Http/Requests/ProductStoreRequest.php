<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required_without:products|max:100|string',
            'barcode' => 'max:100|string|nullable',
            'stock' => 'required_without:products|max:100|numeric',
            'selling_price' => 'required_without:products|numeric',
            'purchase_price' => 'required_without:products|numeric',
            'image' => 'image:jpeg,png,jpg,gif,svg|max:10240',
            'category_id' => 'numeric|nullable',

            'products' => 'array',
            'products.*.name' => 'max:100|string',
            'products.*.uuid' => 'max:100|string',
            'products.*.barcode' => 'required|max:100|string|nullable',
            'products.*.quantity_stok' => 'required|max:100|numeric',
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
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama tidak boleh lebih dari 100 karakter.',
            'name.string' => 'Nama harus berupa string.',

            'barcode.max' => 'Kode batang tidak boleh lebih dari 100 karakter.',
            'barcode.string' => 'Kode batang harus berupa string.',
            'barcode.nullable' => 'Kode batang boleh kosong.',

            'stock.required' => 'Stok wajib diisi.',
            'stock.max' => 'Stok tidak boleh lebih dari 100.',
            'stock.numeric' => 'Stok harus berupa angka.',

            'selling_price.required' => 'Harga jual wajib diisi.',
            'selling_price.numeric' => 'Harga jual harus berupa angka.',

            'purchase_price.required' => 'Harga beli wajib diisi.',
            'purchase_price.numeric' => 'Harga beli harus berupa angka.',


            'image.max' => 'Ukuran file maksimum adalah 2048 kilobyte.',

            'category_id.numeric' => 'ID kategori harus berupa angka.',
            'category_id.nullable' => 'ID kategori boleh kosong.',

            'description.nullable' => 'Deskripsi boleh kosong.',
            'description.max' => 'Deskripsi tidak boleh lebih dari 200 karakter.',
        ];
    }
}
