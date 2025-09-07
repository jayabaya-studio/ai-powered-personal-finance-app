<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Services\AccountService;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
        $this->authorizeResource(Account::class, 'account');
    }

    public function index(Request $request)
    {
        $accounts = $this->accountService->getAccountsForCurrentUser();
        $editAccount = null;

        if ($request->has('edit_id')) {
            try {
                $editAccount = $this->accountService->findById((int)$request->edit_id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return redirect()->route('accounts.index')->with('error', 'Account not found or unauthorized.');
            }
        }

        return view('user.accounts.index', compact('accounts', 'editAccount'));
    }

    public function store(StoreAccountRequest $request)
    {

        $this->accountService->createAccount($request->validated());
        return redirect()->route('accounts.index')->with('success', 'Rekening berhasil ditambahkan!');
    }

    public function edit(Account $account)
    {

        return redirect()->route('accounts.index', ['edit_id' => $account->id]);
    }

    public function update(UpdateAccountRequest $request, Account $account)
    {

        $this->accountService->updateAccount($account->id, $request->validated());
        return redirect()->route('accounts.index')->with('success', 'Rekening berhasil diperbarui!');
    }

    public function destroy(Account $account)
    {

        $this->accountService->deleteAccount($account->id);
        return redirect()->route('accounts.index')->with('success', 'Rekening berhasil dihapus!');
    }
}
