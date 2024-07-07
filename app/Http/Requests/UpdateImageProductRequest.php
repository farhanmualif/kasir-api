<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateImageProductRequest extends FormRequest
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
            "image" => "required|file|mimes:png,jpg,jpeg|max:2048",
        ];
    }

    public function messages(): array
    {
        return [
            'image.required'  => 'Gambar tidak boleh kosong.',
            'image.file'      => 'Harus berupa file.',
            'image.mimes'     => 'Format file harus png, jpg, atau jpeg.',
            'image.max'       => 'Ukuran file maksimum adalah 2MB.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(\responseJson('Validation error', $validator->errors(), false, 422));
    }
}
