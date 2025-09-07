<?php
// File: app/Http/Requests/InviteFamilyMemberRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Family;

class InviteFamilyMemberRequest extends FormRequest
{

    public function authorize(): bool
    {
        $family = $this->route('family');
        return auth()->check() && (auth()->user()->id === $family->owner_user_id || auth()->user()->role === 'admin');
    }

    public function rules(): array
    {
        $familyId = $this->route('family')->id;

        return [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'exists:users,email',
                Rule::unique('family_space_user')->where(function ($query) use ($familyId) {
                    return $query->where('family_space_id', $familyId);
                }),
                Rule::notIn([auth()->user()->email]),
            ],
            'role' => ['nullable', 'string', Rule::in(['member', 'admin'])],
        ];
    }

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
