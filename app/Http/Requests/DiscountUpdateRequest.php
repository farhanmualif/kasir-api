<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscountUpdateRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:100'],
            'type' => ['sometimes', 'string', 'in:percentage,fixed'],
            'value' => ['sometimes', 'numeric', 'min:0'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.string' => 'Judul diskon harus berupa teks',
            'title.max' => 'Judul diskon tidak boleh lebih dari 100 karakter',

            'type.string' => 'Jenis diskon harus berupa teks',
            'type.in' => 'Jenis diskon hanya boleh berupa percentage atau fixed',

            'value.numeric' => 'Nilai diskon harus berupa angka',
            'value.min' => 'Nilai diskon tidak boleh kurang dari 0',

        ];
    }
}
