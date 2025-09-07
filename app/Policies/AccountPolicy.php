<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AccountPolicy
{
    /**
     * Determine whether the user can view any accounts.
     */
    public function viewAny(User $user): bool
    {
        return $user->id !== null;
    }

    /**
     * Determine whether the user can view the account.
     */
    public function view(User $user, Account $account): bool
    {
        // Pengguna bisa melihat akun jika:
        // 1. Akun tersebut adalah akun pribadinya
        // 2. Akun tersebut adalah joint account DAN pengguna adalah anggota dari family_space_id akun tersebut
        return $user->id === $account->user_id
               || ($account->is_joint && $user->current_family_id === $account->family_space_id && $account->familySpace->members->contains($user));
    }

    /**
     * Determine whether the user can create accounts.
     */
    public function create(User $user): bool
    {
        return $user->id !== null; // Pengguna terautentikasi dapat membuat akun
    }

    /**
     * Determine whether the user can update the account.
     * (Untuk memperbarui akun)
     */
    public function update(User $user, Account $account): bool
    {
        // Pengguna bisa memperbarui akun jika:
        // 1. Akun tersebut adalah akun pribadinya
        if ($user->id === $account->user_id) {
            return true;
        }

        // 2. Akun tersebut adalah joint account DAN pengguna adalah owner atau admin dari FamilySpace akun tersebut
        if ($account->is_joint && $user->current_family_id === $account->family_space_id) {
            $family = $account->familySpace;
            // Cek apakah user adalah owner dari family space
            if ($family->owner_user_id === $user->id) {
                return true;
            }
            // Cek apakah user adalah admin di family space (via pivot table)
            return $family->members()->where('user_id', $user->id)->wherePivot('role', 'admin')->exists();
        }
        return false;
    }

    /**
     * Determine whether the user can delete the account.
     * (Untuk menghapus akun)
     */
    public function delete(User $user, Account $account): bool
    {
        // Pengguna bisa menghapus akun jika:
        // 1. Akun tersebut adalah akun pribadinya
        if ($user->id === $account->user_id) {
            return true;
        }

        // 2. Akun tersebut adalah joint account DAN pengguna adalah owner atau admin dari FamilySpace akun tersebut
        if ($account->is_joint && $user->current_family_id === $account->family_space_id) {
            $family = $account->familySpace;
            // Cek apakah user adalah owner dari family space
            if ($family->owner_user_id === $user->id) {
                return true;
            }
            // Cek apakah user adalah admin di family space (via pivot table)
            return $family->members()->where('user_id', $user->id)->wherePivot('role', 'admin')->exists();
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model. (Tidak relevan)
     */
    public function restore(User $user, Account $account): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model. (Tidak relevan)
     */
    public function forceDelete(User $user, Account $account): bool
    {
        return false;
    }
}
