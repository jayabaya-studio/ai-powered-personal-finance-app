<?php
// File: app/Policies/TransactionPolicy.php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TransactionPolicy
{
    /**
     * Determine whether the user can view any models.
     * (Untuk melihat daftar transaksi)
     */
    public function viewAny(User $user): bool
    {
        return $user->id !== null; // Pengguna terautentikasi dapat melihat transaksi
    }

    /**
     * Determine whether the user can view the model.
     * (Untuk melihat detail transaksi tertentu)
     */
    public function view(User $user, Transaction $transaction): bool
    {
        // Pengguna bisa melihat transaksi jika:
        // 1. Itu adalah transaksi pribadinya
        // 2. Transaksi tersebut terkait dengan akun bersama dari Family Space yang aktif
        return $user->id === $transaction->user_id
               || ($transaction->account->is_joint
                   && $user->current_family_id === $transaction->account->family_space_id
                   && $transaction->account->familySpace->members->contains($user));
    }

    /**
     * Determine whether the user can create models.
     * (Untuk membuat transaksi baru)
     */
    public function create(User $user): bool
    {
        return $user->id !== null; // Pengguna terautentikasi dapat membuat transaksi
    }

    /**
     * Determine whether the user can update the model.
     * (Untuk memperbarui transaksi)
     */
    public function update(User $user, Transaction $transaction): bool
    {
        // Pengguna bisa memperbarui transaksi jika:
        // 1. Itu adalah transaksi pribadinya
        if ($user->id === $transaction->user_id) {
            return true;
        }

        // 2. Transaksi tersebut terkait dengan akun bersama DARI Family Space yang aktif DAN pengguna adalah owner/admin
        if ($transaction->account->is_joint && $user->current_family_id === $transaction->account->family_space_id) {
            $family = $transaction->account->familySpace;
            if ($family->owner_user_id === $user->id) {
                return true; // User adalah owner
            }
            // Cek apakah user adalah admin di family space (via pivot table)
            return $family->members()->where('user_id', $user->id)->wherePivot('role', 'admin')->exists();
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * (Untuk menghapus transaksi)
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        // Pengguna bisa menghapus transaksi jika:
        // 1. Itu adalah transaksi pribadinya
        if ($user->id === $transaction->user_id) {
            return true;
        }

        // 2. Transaksi tersebut terkait dengan akun bersama DARI Family Space yang aktif DAN pengguna adalah owner/admin
        if ($transaction->account->is_joint && $user->current_family_id === $transaction->account->family_space_id) {
            $family = $transaction->account->familySpace;
            if ($family->owner_user_id === $user->id) {
                return true; // User adalah owner
            }
            // Cek apakah user adalah admin di family space (via pivot table)
            return $family->members()->where('user_id', $user->id)->wherePivot('role', 'admin')->exists();
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model. (Tidak relevan)
     */
    public function restore(User $user, Transaction $transaction): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model. (Tidak relevan)
     */
    public function forceDelete(User $user, Transaction $transaction): bool
    {
        return false;
    }
}
