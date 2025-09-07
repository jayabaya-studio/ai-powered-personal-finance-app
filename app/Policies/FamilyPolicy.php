<?php

namespace App\Policies;

use App\Models\Family;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FamilyPolicy
{
    /**
     * Determine whether the user can view any models.
     * (Untuk melihat daftar Family Spaces)
     */
    public function viewAny(User $user): bool
    {
        return $user->id !== null; // Hanya pengguna terautentikasi yang bisa melihat daftar Family Spaces
    }

    /**
     * Determine whether the user can view the model.
     * (Untuk melihat detail Family Space tertentu)
     */
    public function view(User $user, Family $family): bool
    {
        // Pengguna bisa melihat Family Space jika mereka adalah owner atau member
        return $user->id === $family->owner_user_id || $family->members->contains($user);
    }

    /**
     * Determine whether the user can create models.
     * (Untuk membuat Family Space baru)
     */
    public function create(User $user): bool
    {
        return $user->id !== null; // Hanya pengguna terautentikasi yang bisa membuat Family Space
    }

    /**
     * Determine whether the user can update the model.
     * (Untuk memperbarui Family Space - misalnya nama)
     */
    public function update(User $user, Family $family): bool
    {
        return $this->isOwnerOrAdmin($user, $family);
    }

    /**
     * Determine whether the user can delete the model.
     * (Untuk menghapus Family Space)
     */
    public function delete(User $user, Family $family): bool
    {
        // Hanya owner Family Space yang bisa menghapus
        return $user->id === $family->owner_user_id;
    }

    /**
     * Determine whether the user can add members to the family.
     */
    public function addMember(User $user, Family $family): bool
    {
        return $this->isOwnerOrAdmin($user, $family);
    }

    /**
     * Determine whether the user can remove members from the family.
     */
    public function removeMember(User $user, Family $family, User $memberToRemove): bool
    {
        // Pengguna tidak bisa menghapus dirinya sendiri melalui aksi ini.
        if ($user->id === $memberToRemove->id) {
            return false;
        }

        // Hanya owner atau admin yang bisa menghapus anggota lain.
        return $this->isOwnerOrAdmin($user, $family);
    }

    /**
     * Determine whether the user can create a joint account within this family space.
     */
    public function createJointAccount(User $user, Family $family): bool
    {
        return $this->isOwnerOrAdmin($user, $family);
    }

    /**
     * Determine whether the user is an owner or an admin of the family.
     * This correctly checks the role from the pivot table.
     */
    protected function isOwnerOrAdmin(User $user, Family $family): bool
    {
        // Cek apakah user adalah owner.
        if ($user->id === $family->owner_user_id) {
            return true;
        }

        // Cek apakah user adalah member dengan peran 'admin'.
        // Ini melakukan query ke pivot table untuk mendapatkan peran.
        return $family->members()->where('user_id', $user->id)->wherePivot('role', 'admin')->exists();
    }
}
