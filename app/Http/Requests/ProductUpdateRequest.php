<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductUpdateRequest extends FormRequest
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
            "name" => "nullable|max:100",
            "barcode" => "nullable|max:15",
            "add_or_reduce_stock" => "required",
            "quantity_stok" => "required",
            "selling_price" => "required|numeric",
            "purchase_price" => "required|numeric",
            "description" => "nullable"
        ];
    }

    public function messages(): array
    {
        return [
            'name.nullable' => 'Nama boleh dikosongkan.',
            'name.max' => 'Nama tidak boleh lebih dari 100 karakter.',
            'barcode.nullable' => 'Kode batang boleh dikosongkan.',
            'barcode.max' => 'Kode batang tidak boleh lebih dari 15 karakter.',
            'add_or_reduce_stock.required' => 'Penambahan atau pengurangan stok wajib dipilih.',
            'quantity_stok.required' => 'Jumlah stok wajib diisi.',
            'selling_price.required' => 'Harga jual wajib diisi.',
            'selling_price.numeric' => 'Harga jual harus berupa angka.',
            'purchase_price.required' => 'Harga beli wajib diisi.',
            'purchase_price.numeric' => 'Harga beli harus berupa angka.',
            'description.nullable' => 'Deskripsi boleh dikosongkan.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(\responseJson('Validation error', $validator->errors(), false, 422));
    }
}
