<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AccountPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->id !== null;
    }

    public function view(User $user, Account $account): bool
    {
        return $user->id === $account->user_id
               || ($account->is_joint && $user->current_family_id === $account->family_space_id && $account->familySpace->members->contains($user));
    }

    public function create(User $user): bool
    {
        return $user->id !== null;
    }

    public function update(User $user, Account $account): bool
    {
        if ($user->id === $account->user_id) {
            return true;
        }

        if ($account->is_joint && $user->current_family_id === $account->family_space_id) {
            $family = $account->familySpace;
            if ($family->owner_user_id === $user->id) {
                return true;
            }
            return $family->members()->where('user_id', $user->id)->wherePivot('role', 'admin')->exists();
        }
        return false;
    }

    public function delete(User $user, Account $account): bool
    {
        if ($user->id === $account->user_id) {
            return true;
        }

        if ($account->is_joint && $user->current_family_id === $account->family_space_id) {
            $family = $account->familySpace;
            if ($family->owner_user_id === $user->id) {
                return true;
            }
            return $family->members()->where('user_id', $user->id)->wherePivot('role', 'admin')->exists();
        }
        return false;
    }

    public function restore(User $user, Account $account): bool
    {
        return false;
    }

    public function forceDelete(User $user, Account $account): bool
    {
        return false;
    }
}
