<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $categoryId = $this->route('category')->id ?? null;

        return [
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['income', 'expense'])],
            'parent_id' => [
                'nullable',
                'exists:categories,id,user_id,' . auth()->id(),
                Rule::notIn([$categoryId]),
            ],
            'icon' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.string' => 'Nama kategori harus berupa teks.',
            'name.max' => 'Nama kategori tidak boleh lebih dari 255 karakter.',
            'type.required' => 'Tipe kategori wajib diisi (Pemasukan atau Pengeluaran).',
            'type.in' => 'Tipe kategori tidak valid.',
            'parent_id.exists' => 'Kategori induk yang dipilih tidak valid.',
            'parent_id.nullable' => 'Kategori induk tidak wajib diisi.',
            'parent_id.not_in' => 'Kategori induk tidak boleh sama dengan kategori yang sedang diedit.',
            'icon.string' => 'Icon harus berupa teks.',
            'icon.max' => 'Icon tidak boleh lebih dari 50 karakter.',
        ];
    }
}