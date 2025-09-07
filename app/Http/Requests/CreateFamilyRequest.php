<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateFamilyRequest extends FormRequest
{

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:family_spaces,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama Family Space wajib diisi.',
            'name.string' => 'Nama Family Space harus berupa teks.',
            'name.max' => 'Nama Family Space tidak boleh lebih dari 255 karakter.',
            'name.unique' => 'Nama Family Space ini sudah digunakan. Mohon pilih nama lain.',
        ];
    }
}
