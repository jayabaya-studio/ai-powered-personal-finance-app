<?php
// File: app/Http/Requests/InviteFamilyMemberRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Family; // Impor model Family

class InviteFamilyMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Hanya owner atau admin dari Family Space yang bisa mengundang anggota
        $family = $this->route('family'); // Ambil family dari route model binding
        return auth()->check() && (auth()->user()->id === $family->owner_user_id || auth()->user()->role === 'admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Asumsi kita mengundang berdasarkan email
        $familyId = $this->route('family')->id;

        return [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'exists:users,email', // Pastikan email terdaftar sebagai user
                // Pastikan user belum menjadi anggota dari family space ini
                Rule::unique('family_space_user')->where(function ($query) use ($familyId) {
                    return $query->where('family_space_id', $familyId);
                }),
                // Pastikan user yang diundang bukan user yang sedang login itu sendiri
                Rule::notIn([auth()->user()->email]),
            ],
            'role' => ['nullable', 'string', Rule::in(['member', 'admin'])], // Bisa diatur sebagai member atau admin
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
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Alamat email harus valid.',
            'email.max' => 'Alamat email tidak boleh lebih dari 255 karakter.',
            'email.exists' => 'Pengguna dengan email ini tidak ditemukan.',
            'email.unique' => 'Pengguna ini sudah menjadi anggota Family Space.',
            'email.not_in' => 'Anda tidak bisa mengundang diri sendiri.',
            'role.in' => 'Peran yang dipilih tidak valid.',
        ];
    }
}
