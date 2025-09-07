<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TransactionPolicy
{

    public function viewAny(User $user): bool
    {
        return $user->id !== null;
    }

    public function view(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id
               || ($transaction->account->is_joint
                   && $user->current_family_id === $transaction->account->family_space_id
                   && $transaction->account->familySpace->members->contains($user));
    }

    public function create(User $user): bool
    {
        return $user->id !== null;
    }

    public function update(User $user, Transaction $transaction): bool
    {
        if ($user->id === $transaction->user_id) {
            return true;
        }

        if ($transaction->account->is_joint && $user->current_family_id === $transaction->account->family_space_id) {
            $family = $transaction->account->familySpace;
            if ($family->owner_user_id === $user->id) {
                return true;
            }
            return $family->members()->where('user_id', $user->id)->wherePivot('role', 'admin')->exists();
        }
        return false;
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        if ($user->id === $transaction->user_id) {
            return true;
        }

        if ($transaction->account->is_joint && $user->current_family_id === $transaction->account->family_space_id) {
            $family = $transaction->account->familySpace;
            if ($family->owner_user_id === $user->id) {
                return true;
            }
            return $family->members()->where('user_id', $user->id)->wherePivot('role', 'admin')->exists();
        }
        return false;
    }

    public function restore(User $user, Transaction $transaction): bool
    {
        return false;
    }

    public function forceDelete(User $user, Transaction $transaction): bool
    {
        return false;
    }
}
