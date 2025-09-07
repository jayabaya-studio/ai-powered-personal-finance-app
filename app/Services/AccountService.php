<?php

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

    public function getAccountsForCurrentUser(): Collection
    {
        $user = Auth::user();
        $accounts = $this->accountRepository->getAllByUser($user->id);

        if ($user->current_family_id) {
            $jointAccounts = $this->accountRepository->getJointAccountsByFamily($user->current_family_id);
            $accounts = $accounts->merge($jointAccounts);
        }
        return $accounts->unique('id');
    }

    public function getUserAccounts(): Collection
    {
        return $this->accountRepository->getAllByUser(Auth::id());
    }

    public function createAccount(array $data): Account
    {
        $user = Auth::user();
        $data['user_id'] = $user->id;
        $data['family_space_id'] = null;
        $data['is_joint'] = false;

        if (isset($data['is_joint']) && $data['is_joint'] && $user->current_family_id) {
            $data['family_space_id'] = $user->current_family_id;
        }

        return $this->accountRepository->create($data);
    }

    public function updateAccount(int $id, array $data): Account
    {
        $account = $this->findById($id);

        if (isset($data['is_joint']) && $account->is_joint !== $data['is_joint']) {

        }

        return $this->accountRepository->update($id, $data);
    }

    public function deleteAccount(int $id): bool
    {
        return $this->accountRepository->delete($id);
    }

    public function findById(int $id): Account
    {
        return $this->accountRepository->findById($id);
    }
}
