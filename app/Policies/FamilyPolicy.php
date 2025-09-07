<?php

namespace App\Policies;

use App\Models\Family;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FamilyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->id !== null;
    }

    public function view(User $user, Family $family): bool
    {
        return $user->id === $family->owner_user_id || $family->members->contains($user);
    }

    public function create(User $user): bool
    {
        return $user->id !== null;
    }

    public function update(User $user, Family $family): bool
    {
        return $this->isOwnerOrAdmin($user, $family);
    }

    public function delete(User $user, Family $family): bool
    {
        return $user->id === $family->owner_user_id;
    }

    public function addMember(User $user, Family $family): bool
    {
        return $this->isOwnerOrAdmin($user, $family);
    }

    public function removeMember(User $user, Family $family, User $memberToRemove): bool
    {
        if ($user->id === $memberToRemove->id) {
            return false;
        }

        return $this->isOwnerOrAdmin($user, $family);
    }

    public function createJointAccount(User $user, Family $family): bool
    {
        return $this->isOwnerOrAdmin($user, $family);
    }

    protected function isOwnerOrAdmin(User $user, Family $family): bool
    {
        if ($user->id === $family->owner_user_id) {
            return true;
        }

        return $family->members()->where('user_id', $user->id)->wherePivot('role', 'admin')->exists();
    }
}
