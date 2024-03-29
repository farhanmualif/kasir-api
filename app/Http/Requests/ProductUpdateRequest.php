<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
}
