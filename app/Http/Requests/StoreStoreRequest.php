<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoreRequest extends FormRequest
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
            "name" =>  'required|max:100|string',
            "email" =>  'required|max:100|email',
            "address" =>  'required|max:100|string',
            "password" => 'required|max:100|string',
            "confirm_password" => 'required|max:100|string'

        ];
    }
}
