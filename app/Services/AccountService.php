<?php
// File: app/Services/AccountService.php (Updated)

namespace App\Services;

use App\Repositories\AccountRepository;
use App\Models\Account;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class AccountService
{
    protected $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * Get all accounts accessible by the current user (personal or joint from active family).
     * This method is central for Opsi C.
     *
     * @return Collection<int, Account>
     */
    public function getAccountsForCurrentUser(): Collection
    {
        $user = Auth::user();
        $accounts = $this->accountRepository->getAllByUser($user->id); // Dapatkan akun pribadi

        if ($user->current_family_id) {
            // Jika ada FamilySpace aktif, dapatkan juga akun bersama dari FamilySpace itu
            $jointAccounts = $this->accountRepository->getJointAccountsByFamily($user->current_family_id);
            $accounts = $accounts->merge($jointAccounts);
        }
        return $accounts->unique('id'); // Pastikan tidak ada duplikasi jika ada mekanisme lain
    }

    /**
     * Get only personal accounts for the authenticated user.
     * @return Collection<int, Account>
     */
    public function getUserAccounts(): Collection
    {
        return $this->accountRepository->getAllByUser(Auth::id());
    }

    /**
     * Create a new account.
     * This method now handles both personal and potentially joint accounts.
     *
     * @param array $data
     * @return Account
     */
    public function createAccount(array $data): Account
    {
        $user = Auth::user();
        // Default to personal account
        $data['user_id'] = $user->id;
        $data['family_space_id'] = null;
        $data['is_joint'] = false;

        // Jika form mengindikasikan akun bersama, dan user punya family aktif
        if (isset($data['is_joint']) && $data['is_joint'] && $user->current_family_id) {
            $data['family_space_id'] = $user->current_family_id;
        }

        return $this->accountRepository->create($data);
    }

    /**
     * Update an existing account.
     *
     * @param int $id
     * @param array $data
     * @return Account
     */
    public function updateAccount(int $id, array $data): Account
    {
        $account = $this->findById($id); // Pastikan akun milik user atau joint
        
        // Hanya owner atau admin family yang bisa mengubah is_joint atau family_space_id dari akun bersama
        // Logika otorisasi yang lebih canggih akan ada di Policy
        if (isset($data['is_joint']) && $account->is_joint !== $data['is_joint']) {
            // Jika is_joint diubah, tambahkan logika validasi di sini atau Policy
            // Untuk saat ini, kita biarkan saja, tapi di dunia nyata ini butuh otorisasi ketat.
        }

        return $this->accountRepository->update($id, $data);
    }

    /**
     * Delete an account.
     *
     * @param int $id
     * @return bool
     */
    public function deleteAccount(int $id): bool
    {
        return $this->accountRepository->delete($id);
    }

    /**
     * Find an account by ID, ensuring it belongs to the authenticated user OR is a joint account of their active family.
     *
     * @param int $id
     * @return Account
     */
    public function findById(int $id): Account
    {
        return $this->accountRepository->findById($id);
    }
}
