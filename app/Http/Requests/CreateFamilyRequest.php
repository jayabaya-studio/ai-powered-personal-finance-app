<?php
// File: app/Http/Requests/CreateFamilyRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateFamilyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Izinkan hanya pengguna yang terautentikasi untuk membuat Family Space
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:family_spaces,name',
            // Kita tidak memerlukan 'owner_user_id' di sini karena akan otomatis diatur di service/repository
            // 'slug' jika Anda memutuskan untuk menambahkannya ke tabel family_spaces
            // 'slug' => 'nullable|string|max:255|unique:family_spaces,slug',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
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
