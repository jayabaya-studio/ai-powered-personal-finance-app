<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Untuk saat ini, kita akan mengizinkan semua permintaan yang diautentikasi.
        // Logika otorisasi yang lebih kompleks dapat ditambahkan di sini jika diperlukan.
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'nullable|numeric|min:0', // Dapat diisi awal atau dibiarkan kosong
            'target_date' => 'nullable|date|after_or_equal:today', // Tanggal target harus hari ini atau setelahnya
            'description' => 'nullable|string|max:1000',
            // 'is_completed' tidak disertakan dalam validasi ini karena akan dikelola oleh logika aplikasi
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama tujuan wajib diisi.',
            'name.string' => 'Nama tujuan harus berupa teks.',
            'name.max' => 'Nama tujuan tidak boleh lebih dari 255 karakter.',
            'target_amount.required' => 'Jumlah target tujuan wajib diisi.',
            'target_amount.numeric' => 'Jumlah target harus berupa angka.',
            'target_amount.min' => 'Jumlah target harus lebih besar dari 0.',
            'current_amount.numeric' => 'Jumlah saat ini harus berupa angka.',
            'current_amount.min' => 'Jumlah saat ini tidak boleh kurang dari 0.',
            'target_date.date' => 'Tanggal target harus berupa tanggal yang valid.',
            'target_date.after_or_equal' => 'Tanggal target harus hari ini atau setelahnya.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max' => 'Deskripsi tidak boleh lebih dari 1000 karakter.',
        ];
    }
}
