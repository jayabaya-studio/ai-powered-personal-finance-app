<?php

namespace App\Services;

use App\Repositories\FamilyRepository;
use App\Repositories\AccountRepository;
use App\Models\Family;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class FamilyService
{
    protected $familyRepository;
    protected $accountRepository;

    public function __construct(FamilyRepository $familyRepository, AccountRepository $accountRepository)
    {
        $this->familyRepository = $familyRepository;
        $this->accountRepository = $accountRepository;
    }

    public function getUserFamilySpaces(): Collection
    {
        return $this->familyRepository->getUserFamilySpaces();
    }

    public function createFamily(array $data): Family
    {
        return $this->familyRepository->create($data);
    }

    public function findFamilyById(int $id): Family
    {
        return $this->familyRepository->findById($id);
    }

    public function addFamilyMember(Family $family, User $invitedUser, string $role = 'member'): bool
    {
        return $this->familyRepository->inviteMember($family, $invitedUser, $role);
    }

    public function removeFamilyMember(Family $family, User $member): bool
    {
        return $this->familyRepository->removeMember($family, $member);
    }

    public function setCurrentFamilySpace(User $user, ?int $familyId): bool
    {
        if ($familyId !== null) {
            $family = $this->familyRepository->findById($familyId);
        }

        $user->current_family_id = $familyId;
        $user->save();
        return true;
    }

    public function createJointAccount(Family $family, array $data): \App\Models\Account
    {
        if (!$family) {
            throw new InvalidArgumentException('Family is required to create a joint account.');
        }

        $data['user_id'] = Auth::id();
        $data['family_space_id'] = $family->id;
        $data['is_joint'] = true;

        return $this->accountRepository->create($data);
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
}
