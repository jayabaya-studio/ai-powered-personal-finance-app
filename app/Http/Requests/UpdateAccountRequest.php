<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['checking', 'savings', 'credit_card', 'cash', 'investment'])],
            'balance' => 'required|numeric|min:0', // Saldo diizinkan di update, tapi hati-hati!
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama rekening wajib diisi.',
            'name.string' => 'Nama rekening harus berupa teks.',
            'name.max' => 'Nama rekening tidak boleh lebih dari 255 karakter.',
            'type.required' => 'Tipe rekening wajib diisi.',
            'type.in' => 'Tipe rekening tidak valid.',
            'balance.required' => 'Saldo harus diisi.',
            'balance.numeric' => 'Saldo harus berupa angka.',
            'balance.min' => 'Saldo tidak boleh kurang dari 0.',
        ];
    }
}