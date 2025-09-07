<?php
// File: app/Repositories/AccountRepository.php (Updated)

namespace App\Repositories;

use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class AccountRepository
{
    /**
     * Get all personal accounts for the authenticated user.
     *
     * @return Collection<int, Account>
     */
    public function getAllByUser(int $userId): Collection
    {
        return Account::where('user_id', $userId)
                      ->whereNull('family_space_id') // Hanya akun pribadi
                      ->get();
    }

    /**
     * Get all joint accounts for a specific family space.
     *
     * @param int $familySpaceId
     * @return Collection<int, Account>
     */
    public function getJointAccountsByFamily(int $familySpaceId): Collection
    {
        return Account::where('family_space_id', $familySpaceId)
                      ->where('is_joint', true)
                      ->get();
    }

    /**
     * Create a new account.
     *
     * @param array $data
     * @return Account
     */
    public function create(array $data): Account
    {
        // user_id harus selalu diatur, bahkan untuk joint account
        // family_space_id dan is_joint diatur oleh caller (FamilyService atau AccountService)
        return Account::create($data);
    }

    /**
     * Find an account by its ID, ensuring it belongs to the authenticated user OR is a joint account of their active family.
     * This method might need further refinement based on the exact authorization logic.
     *
     * @param int $id
     * @return Account
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findById(int $id): Account
    {
        $user = Auth::user();

        // Coba temukan akun pribadi user
        $account = Account::where('id', $id)->where('user_id', $user->id)->first();

        if ($account) {
            return $account;
        }

        // Jika tidak ditemukan akun pribadi, coba cari di joint accounts dari current_family_id user
        if ($user->current_family_id) {
            $account = Account::where('id', $id)
                              ->where('family_space_id', $user->current_family_id)
                              ->where('is_joint', true)
                              ->first();
            if ($account) {
                return $account;
            }
        }

        // Jika tidak ditemukan sama sekali, lempar exception
        throw (new \Illuminate\Database\Eloquent\ModelNotFoundException)->setModel(Account::class, $id);
    }

    /**
     * Update an existing account.
     *
     * @param int $id
     * @param array $data
     * @return Account
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, array $data): Account
    {
        $account = $this->findById($id); // Gunakan findById yang sudah diperbarui
        $account->update($data);
        return $account;
    }

    /**
     * Delete an account.
     *
     * @param int $id
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): bool
    {
        $account = $this->findById($id); // Gunakan findById yang sudah diperbarui
        return $account->delete();
    }
}
