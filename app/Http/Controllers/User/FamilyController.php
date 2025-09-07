<?php
// File: app/Http/Controllers/User/FamilyController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateFamilyRequest;
use App\Http\Requests\InviteFamilyMemberRequest;
use App\Services\FamilyService;
use App\Services\AccountService; // Akan dibutuhkan untuk akun bersama
use App\Models\Family;
use App\Models\User; // Impor model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FamilyController extends Controller
{
    protected $familyService;
    protected $accountService;

    public function __construct(FamilyService $familyService, AccountService $accountService)
    {
        $this->familyService = $familyService;
        $this->accountService = $accountService;
        // Otorisasi akan ditangani oleh Policies untuk sebagian besar aksi
        $this->authorizeResource(Family::class, 'family');
    }

    /**
     * Display a list of family spaces the user is a member of.
     */
    public function index()
    {
        $user = Auth::user();
        $myFamilySpaces = $this->familyService->getUserFamilySpaces();
        $currentFamily = $user->currentFamilySpace;

        return view('user.families.index', compact('myFamilySpaces', 'currentFamily'));
    }

    /**
     * Show the form for creating a new family space.
     */
    public function create()
    {
        return view('user.families.create');
    }

    /**
     * Store a newly created family space in storage.
     */
    public function store(CreateFamilyRequest $request)
    {
        $family = $this->familyService->createFamily($request->validated());
        return redirect()->route('families.index')->with('success', 'Family Space "' . $family->name . '" created successfully and set as active!');
    }

    /**
     * Display the specified family space and its members.
     */
    public function show(Family $family)
    {
        // Kebijakan 'view' akan memastikan user adalah anggota.
        // Eager load members untuk ditampilkan
        $family->load('members', 'owner');
        // Mendapatkan semua akun yang dapat diakses oleh user dalam Family Space ini
        $accounts = $this->accountService->getAccountsForCurrentUser($family->id); // Asumsi AccountService memiliki method ini
        $usersInFamily = $family->members;

        // Mendapatkan akun bersama dari family ini
        $jointAccounts = $family->jointAccounts;

        return view('user.families.show', compact('family', 'accounts', 'jointAccounts', 'usersInFamily'));
    }

    /**
     * Update the specified family space in storage. (e.g., update name)
     */
    public function update(CreateFamilyRequest $request, Family $family) // Menggunakan CreateFamilyRequest untuk validasi nama
    {
        // Kebijakan 'update' akan memastikan user adalah owner/admin
        $family->update($request->validated());
        return redirect()->route('families.show', $family)->with('success', 'Family Space updated successfully.');
    }

    /**
     * Remove the specified family space from storage.
     */
    public function destroy(Family $family)
    {
        // Kebijakan 'delete' akan memastikan user adalah owner
        // The deletion logic is straightforward, so we can perform it here.
        // The associated members and joint accounts will be handled by model events or database constraints if set up.
        $family->delete();
        return redirect()->route('families.index')->with('success', 'Family Space deleted successfully.');
    }

    /**
     * Handle inviting a member to a family space.
     */
    public function inviteMember(InviteFamilyMemberRequest $request, Family $family)
    {
        // Kebijakan 'addMember' akan memastikan user adalah owner/admin
        $invitedUser = User::where('email', $request->email)->firstOrFail();
        $this->familyService->addFamilyMember($family, $invitedUser, $request->role ?? 'member');
        return redirect()->route('families.show', $family)->with('success', 'Member invited successfully.');
    }

    /**
     * Handle removing a member from a family space.
     */
    public function removeMember(Request $request, Family $family, User $member)
    {
        // Kebijakan 'removeMember' akan memastikan user adalah owner/admin dan bukan owner itu sendiri
        $this->authorize('removeMember', [$family, $member]);

        $this->familyService->removeFamilyMember($family, $member);
        return redirect()->route('families.show', $family)->with('success', 'Member removed successfully.');
    }

    /**
     * Set the current active family space for the user.
     */
    public function setCurrent(Request $request, Family $family)
    {
        // Pastikan user adalah anggota dari family space ini
        $this->authorize('view', $family); // Gunakan kebijakan view untuk memastikan user anggota

        Auth::user()->current_family_id = $family->id;
        Auth::user()->save();

        return redirect()->back()->with('success', 'Family Space "' . $family->name . '" set as active.');
    }

    /**
     * Clear the current active family space for the user.
     */
    public function clearCurrent(Request $request)
    {
        Auth::user()->current_family_id = null;
        Auth::user()->save();

        return redirect()->back()->with('success', 'Active Family Space cleared.');
    }

    /**
     * Show form to create a joint account within a family space.
     */
    public function createJointAccountForm(Family $family)
    {
        $this->authorize('createJointAccount', $family); // Otorisasi untuk membuat joint account
        $accounts = Auth::user()->accounts; // Hanya akun pribadi user yang bisa dilihat untuk form
        return view('user.families.create-joint-account', compact('family', 'accounts'));
    }

    /**
     * Store a new joint account for the specified family space.
     */
    public function storeJointAccount(CreateAccountRequest $request, Family $family) // Menggunakan CreateAccountRequest
    {
        $this->authorize('createJointAccount', $family); // Otorisasi

        $this->familyService->createJointAccount($family, $request->validated());
        return redirect()->route('families.show', $family)->with('success', 'Joint Account created successfully.');
    }
}
