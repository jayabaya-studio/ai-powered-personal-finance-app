<?php

namespace App\Repositories;

use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class AccountRepository
{

    public function getAllByUser(int $userId): Collection
    {
        return Account::where('user_id', $userId)
                      ->whereNull('family_space_id')
                      ->get();
    }

    public function getJointAccountsByFamily(int $familySpaceId): Collection
    {
        return Account::where('family_space_id', $familySpaceId)
                      ->where('is_joint', true)
                      ->get();
    }

    public function create(array $data): Account
    {
        return Account::create($data);
    }

    public function findById(int $id): Account
    {
        $user = Auth::user();

        $account = Account::where('id', $id)->where('user_id', $user->id)->first();

        if ($account) {
            return $account;
        }

        if ($user->current_family_id) {
            $account = Account::where('id', $id)
                              ->where('family_space_id', $user->current_family_id)
                              ->where('is_joint', true)
                              ->first();
            if ($account) {
                return $account;
            }
        }

        throw (new \Illuminate\Database\Eloquent\ModelNotFoundException)->setModel(Account::class, $id);
    }

    public function update(int $id, array $data): Account
    {
        $account = $this->findById($id);
        $account->update($data);
        return $account;
    }

    public function delete(int $id): bool
    {
        $account = $this->findById($id);
        return $account->delete();
    }
}
