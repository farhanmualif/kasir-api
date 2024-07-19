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
            "name" => 'required|max:100|string',
            "email" => 'required|max:100|email',
            "address" => 'required|max:100|string',
            "password" => 'required|max:100|string|confirmed',
            "password_confirmation" => 'required|string'
        ];
    }

    public function messages(): array
    {
        return [
            "name.required" => "nama wajib diisi.",
            "name.max" => "Panjang maksimal nama adalah 100 karakter.",
            "name.string" => "nama harus berupa teks.",

            "email.required" => "email wajib diisi.",
            "email.max" => "Panjang maksimal email adalah 100 karakter.",
            "email.email" => "Format email tidak valid.",

            "address.required" => "alamat wajib diisi.",
            "address.max" => "Panjang maksimal alamat adalah 100 karakter.",
            "address.string" => "alamat harus berupa teks.",

            "password.required" => "password wajib diisi.",
            "password.max" => "Panjang maksimal password adalah 100 karakter.",
            "password.string" => "password harus berupa teks.",
            "password.confirmed" => "Konfirmasi password tidak cocok dengan password.",

            "password_confirmation.required" => "konfirmasi password wajib diisi.",
            "password_confirmation.string" => "konfirmasi password harus berupa teks."
        ];
    }
}
