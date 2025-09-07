<?php
// File: app/Services/FamilyService.php

namespace App\Services;

use App\Repositories\FamilyRepository;
use App\Repositories\AccountRepository; // Akan dibutuhkan nanti untuk update akun
use App\Models\Family;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class FamilyService
{
    protected $familyRepository;
    protected $accountRepository; // Inject AccountRepository

    public function __construct(FamilyRepository $familyRepository, AccountRepository $accountRepository)
    {
        $this->familyRepository = $familyRepository;
        $this->accountRepository = $accountRepository; // Initialize AccountRepository
    }

    /**
     * Get all family spaces where the authenticated user is a member.
     *
     * @return Collection<int, Family>
     */
    public function getUserFamilySpaces(): Collection
    {
        return $this->familyRepository->getUserFamilySpaces();
    }

    /**
     * Create a new family space.
     *
     * @param array $data
     * @return Family
     */
    public function createFamily(array $data): Family
    {
        return $this->familyRepository->create($data);
    }

    /**
     * Find a family space by ID, ensuring the authenticated user is a member.
     *
     * @param int $id
     * @return Family
     */
    public function findFamilyById(int $id): Family
    {
        return $this->familyRepository->findById($id);
    }

    /**
     * Add a member to a family space.
     *
     * @param Family $family
     * @param User $invitedUser
     * @param string $role
     * @return bool
     */
    public function addFamilyMember(Family $family, User $invitedUser, string $role = 'member'): bool
    {
        return $this->familyRepository->inviteMember($family, $invitedUser, $role);
    }

    /**
     * Remove a member from a family space.
     *
     * @param Family $family
     * @param User $member
     * @return bool
     */
    public function removeFamilyMember(Family $family, User $member): bool
    {
        return $this->familyRepository->removeMember($family, $member);
    }

    /**
     * Update the user's current active family space.
     *
     * @param User $user
     * @param int|null $familyId
     * @return bool
     */
    public function setCurrentFamilySpace(User $user, ?int $familyId): bool
    {
        // Pastikan familyId yang diberikan adalah valid dan user adalah anggota dari family tersebut
        if ($familyId !== null) {
            $family = $this->familyRepository->findById($familyId); // Ini akan memvalidasi kepemilikan
        }

        $user->current_family_id = $familyId;
        $user->save();
        return true;
    }

    /**
     * Create a joint account for a family.
     *
     * @param Family $family
     * @param array $data
     * @return \App\Models\Account
     * @throws InvalidArgumentException
     */
    public function createJointAccount(Family $family, array $data): \App\Models\Account
    {
        if (!$family) {
            throw new InvalidArgumentException('Family is required to create a joint account.');
        }

        // Akun bersama harus memiliki family_space_id dan is_joint = true
        $data['user_id'] = Auth::id(); // User yang membuat akun ini tetap tercatat
        $data['family_space_id'] = $family->id;
        $data['is_joint'] = true;

        return $this->accountRepository->create($data);
    }

    /**
     * Get accounts accessible by the current user (personal or joint from active family).
     *
     * @return Collection
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
}
