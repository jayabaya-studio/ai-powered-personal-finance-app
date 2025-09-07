<?php

namespace App\Repositories;

use App\Models\Family;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FamilyRepository
{
    public function getUserFamilySpaces(): Collection
    {
        return Auth::user()->familySpaces()->get();
    }

    public function create(array $data): Family
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();

            $family = Family::create([
                'name' => $data['name'],
                'owner_user_id' => $user->id,
            ]);

            $family->members()->attach($user->id, ['role' => 'owner']);

            $user->current_family_id = $family->id;
            $user->save();

            return $family;
        });
    }

    public function findById(int $id): Family
    {
        $user = Auth::user();
        $family = $user->familySpaces()->where('family_spaces.id', $id)->firstOrFail();
        return $family;
    }

    public function inviteMember(Family $family, User $invitedUser, string $role = 'member'): bool
    {
        if (!$family->members->contains($invitedUser->id)) {
            $family->members()->attach($invitedUser->id, ['role' => $role]);
            return true;
        }
        return false;
    }

    public function removeMember(Family $family, User $member): bool
    {
        if ($family->owner_user_id === $member->id) {
            throw new \InvalidArgumentException("Cannot remove Family Space owner.");
        }
        if ($member->current_family_id === $family->id) {
            $member->current_family_id = null;
            $member->save();
        }
        return $family->members()->detach($member->id);
    }
}
