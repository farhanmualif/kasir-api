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
            'title' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:percentage,fixed',
            'value' => [
                'sometimes',
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ($this->input('type') === 'percentage' && ($value < 0 || $value > 100)) {
                        $fail('Persentase diskon harus antara 0-100%');
                    }
                    if ($this->input('type') === 'fixed' && $value < 0) {
                        $fail('Potongan harga tidak boleh negatif');
                    }
                }
            ],
            'description' => 'nullable|string',
        ];
    }
}
