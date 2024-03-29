<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            "name" =>  'required|max:100|string',
            "barcode" =>  'max:100|string',
            "stock" =>  'required|max:100|numeric',
            "selling_price" =>  'required|numeric',
            "purchase_price" =>  'required|numeric',
            "image" =>  'image:jpeg,png,jpg,gif,svg|max:2048',
            "category_id" => 'numeric|nullable',
            "description" => 'nullable|max:200',
        ];
    }
}
