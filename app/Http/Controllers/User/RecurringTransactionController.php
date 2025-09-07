<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRecurringTransactionRequest;
use App\Models\RecurringTransaction;
use App\Services\RecurringTransactionService;
use App\Services\AccountService;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class RecurringTransactionController extends Controller
{
    protected $service;
    protected $accountService;
    protected $categoryService;

    public function __construct(
        RecurringTransactionService $service,
        AccountService $accountService,
        CategoryService $categoryService
    ) {
        $this->service = $service;
        $this->accountService = $accountService;
        $this->categoryService = $categoryService;
        // Jika Anda membuat Policy, aktifkan baris ini
        // $this->authorizeResource(RecurringTransaction::class, 'recurring_transaction');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $recurringTransactions = $this->service->getAllForUser();
        $accounts = $this->accountService->getAccountsForCurrentUser();
        $categories = $this->categoryService->getCategoriesForForm();

        return view('user.recurring.index', compact('recurringTransactions', 'accounts', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRecurringTransactionRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->route('recurring-transactions.index')->with('success', 'Recurring transaction added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRecurringTransactionRequest $request, RecurringTransaction $recurringTransaction)
    {
        $this->authorize('update', $recurringTransaction);
        $this->service->update($recurringTransaction->id, $request->validated());
        return redirect()->route('recurring-transactions.index')->with('success', 'Recurring transaction updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RecurringTransaction $recurringTransaction)
    {
        $this->authorize('delete', $recurringTransaction);
        $this->service->delete($recurringTransaction->id);
        return redirect()->route('recurring-transactions.index')->with('success', 'Recurring transaction deleted successfully.');
    }
}
