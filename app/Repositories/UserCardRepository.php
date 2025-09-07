<?php

namespace App\Repositories;

use App\Models\UserCard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class UserCardRepository
{
    public function getAllByUser(): Collection
    {
        return UserCard::where('user_id', Auth::id())->latest()->get();
    }

    public function create(array $data): UserCard
    {
        $data['user_id'] = Auth::id();
        return UserCard::create($data);
    }

    public function findById(int $id): UserCard
    {
        return UserCard::where('user_id', Auth::id())->findOrFail($id);
    }

    public function update(int $id, array $data): bool
    {
        return $this->findById($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->findById($id)->delete();
    }

    public function removeDefaultFlagFromAll(): void
    {
        UserCard::where('user_id', Auth::id())->update(['is_default' => false]);
    }
}
