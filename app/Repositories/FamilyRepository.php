<?php
// File: app/Repositories/FamilyRepository.php

namespace App\Repositories;

use App\Models\Family;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB; // Impor DB facade

class FamilyRepository
{
    /**
     * Get all family spaces where the authenticated user is a member.
     *
     * @return Collection<int, Family>
     */
    public function getUserFamilySpaces(): Collection
    {
        return Auth::user()->familySpaces()->get();
    }

    /**
     * Create a new family space and set the current user as the owner and a member.
     *
     * @param array $data
     * @return Family
     */
    public function create(array $data): Family
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();

            // Buat Family Space
            $family = Family::create([
                'name' => $data['name'],
                'owner_user_id' => $user->id,
            ]);

            // Tambahkan owner sebagai member ke tabel pivot family_space_user
            // Kita juga bisa menyimpan role di tabel pivot jika diperlukan
            $family->members()->attach($user->id, ['role' => 'owner']); // Set role di tabel pivot

            // Set current_family_id untuk user yang membuat family space
            $user->current_family_id = $family->id;
            // Jika Anda memiliki kolom 'role' di tabel users yang menandakan role default
            // atau role dalam konteks keluarga, Anda bisa mengaturnya di sini.
            // Contoh: $user->role = 'family_owner'; // Jika ada kolom role di tabel users
            $user->save();

            return $family;
        });
    }

    /**
     * Find a family space by its ID.
     *
     * @param int $id
     * @return Family
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findById(int $id): Family
    {
        // Pastikan user adalah anggota dari family space ini
        $user = Auth::user();
        // Menggunakan with('members') untuk eager load jika diperlukan di tempat lain
        $family = $user->familySpaces()->where('family_spaces.id', $id)->firstOrFail();
        return $family;
    }

    /**
     * Invite a user to a family space.
     * This method assumes you'll have a mechanism to verify the invited user (e.g., email confirmation).
     * For now, it simply attaches the user to the family.
     *
     * @param Family $family
     * @param User $invitedUser
     * @param string $role
     * @return bool
     */
    public function inviteMember(Family $family, User $invitedUser, string $role = 'member'): bool
    {
        // Pastikan user belum menjadi anggota
        if (!$family->members->contains($invitedUser->id)) {
            $family->members()->attach($invitedUser->id, ['role' => $role]); // Simpan role di tabel pivot
            return true;
        }
        return false;
    }

    /**
     * Remove a member from a family space.
     *
     * @param Family $family
     * @param User $member
     * @return bool
     */
    public function removeMember(Family $family, User $member): bool
    {
        // Pastikan member bukan owner
        if ($family->owner_user_id === $member->id) {
            throw new \InvalidArgumentException("Tidak dapat menghapus pemilik Family Space.");
        }
        // Jika member memiliki current_family_id yang merujuk ke family ini, reset
        if ($member->current_family_id === $family->id) {
            $member->current_family_id = null;
            $member->save();
        }
        return $family->members()->detach($member->id);
    }
}
