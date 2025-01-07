<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscountStoreRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
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
            'title.required' => 'Judul diskon wajib diisi',
            'title.string' => 'Judul diskon harus berupa teks',
            'title.max' => 'Judul diskon tidak boleh lebih dari 100 karakter',

            'type.required' => 'Jenis diskon wajib diisi',
            'type.string' => 'Jenis diskon harus berupa teks',
            'type.in' => 'Jenis diskon hanya boleh berupa percentage atau fixed',

            'value.required' => 'Nilai diskon wajib diisi',
            'value.numeric' => 'Nilai diskon harus berupa angka',
            'value.min' => 'Nilai diskon tidak boleh kurang dari 0',
        ];
    }
}
