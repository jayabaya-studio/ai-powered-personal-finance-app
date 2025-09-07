<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateFamilyRequest;
use App\Http\Requests\InviteFamilyMemberRequest;
use App\Services\FamilyService;
use App\Services\AccountService;
use App\Models\Family;
use App\Models\User;
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
        $this->authorizeResource(Family::class, 'family');
    }

    public function index()
    {
        $user = Auth::user();
        $myFamilySpaces = $this->familyService->getUserFamilySpaces();
        $currentFamily = $user->currentFamilySpace;

        return view('user.families.index', compact('myFamilySpaces', 'currentFamily'));
    }

    public function create()
    {
        return view('user.families.create');
    }

    public function store(CreateFamilyRequest $request)
    {
        $family = $this->familyService->createFamily($request->validated());
        return redirect()->route('families.index')->with('success', 'Family Space "' . $family->name . '" created successfully and set as active!');
    }

    public function show(Family $family)
    {
        $family->load('members', 'owner');
        $accounts = $this->accountService->getAccountsForCurrentUser($family->id);
        $usersInFamily = $family->members;

        $jointAccounts = $family->jointAccounts;

        return view('user.families.show', compact('family', 'accounts', 'jointAccounts', 'usersInFamily'));
    }

    public function update(CreateFamilyRequest $request, Family $family)
    {

        $family->update($request->validated());
        return redirect()->route('families.show', $family)->with('success', 'Family Space updated successfully.');
    }

    public function destroy(Family $family)
    {

        $family->delete();
        return redirect()->route('families.index')->with('success', 'Family Space deleted successfully.');
    }

    public function inviteMember(InviteFamilyMemberRequest $request, Family $family)
    {
        $invitedUser = User::where('email', $request->email)->firstOrFail();
        $this->familyService->addFamilyMember($family, $invitedUser, $request->role ?? 'member');
        return redirect()->route('families.show', $family)->with('success', 'Member invited successfully.');
    }

    public function removeMember(Request $request, Family $family, User $member)
    {
        $this->authorize('removeMember', [$family, $member]);

        $this->familyService->removeFamilyMember($family, $member);
        return redirect()->route('families.show', $family)->with('success', 'Member removed successfully.');
    }

    public function setCurrent(Request $request, Family $family)
    {

        $this->authorize('view', $family);

        Auth::user()->current_family_id = $family->id;
        Auth::user()->save();

        return redirect()->back()->with('success', 'Family Space "' . $family->name . '" set as active.');
    }

    public function clearCurrent(Request $request)
    {
        Auth::user()->current_family_id = null;
        Auth::user()->save();

        return redirect()->back()->with('success', 'Active Family Space cleared.');
    }

    public function createJointAccountForm(Family $family)
    {
        $this->authorize('createJointAccount', $family);
        $accounts = Auth::user()->accounts;
        return view('user.families.create-joint-account', compact('family', 'accounts'));
    }

    public function storeJointAccount(CreateAccountRequest $request, Family $family)
    {
        $this->authorize('createJointAccount', $family);

        $this->familyService->createJointAccount($family, $request->validated());
        return redirect()->route('families.show', $family)->with('success', 'Joint Account created successfully.');
    }
}
