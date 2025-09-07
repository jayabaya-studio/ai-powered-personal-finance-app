<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\AccountService;
use App\Services\CategoryService;
use App\Services\TransactionService;
use App\Services\GoalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    protected $transactionService;
    protected $accountService;
    protected $categoryService;
    protected $goalService;

    public function __construct(
        TransactionService $transactionService,
        AccountService $accountService,
        CategoryService $categoryService,
        GoalService $goalService
    ) {
        $this->transactionService = $transactionService;
        $this->accountService = $accountService;
        $this->categoryService = $categoryService;
        $this->goalService = $goalService;

        $this->authorizeResource(Transaction::class, 'transaction');
    }

    public function index(Request $request)
    {
        $transactions = $this->transactionService->getUserTransactionsPaginated();
        $accounts = $this->accountService->getAccountsForCurrentUser();
        $categories = $this->categoryService->getCategoriesForForm();
        $goals = $this->goalService->getUserGoalsWithProgress();

        $editTransaction = null;

        if ($request->has('edit_id')) {
            try {
                $editTransaction = $this->transactionService->findById((int)$request->edit_id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return redirect()->route('transactions.index')->with('error', 'Transaction not found or unauthorized.');
            }
        }
        
        return view('user.transactions.index', compact('transactions', 'accounts', 'categories', 'goals', 'editTransaction'));
    }

    public function store(StoreTransactionRequest $request)
    {
        $this->authorize('view', $this->accountService->findById($request->account_id));
        if ($request->type === 'transfer') {
            $this->authorize('view', $this->accountService->findById($request->transfer_to_account_id));
        }

        $this->transactionService->createTransaction($request->validated());
        return redirect()->route('transactions.index')->with('success', 'Transaction added successfully.');
    }

    public function edit(Transaction $transaction)
    {
        return redirect()->route('transactions.index', ['edit_id' => $transaction->id]);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {

        $this->authorize('view', $this->accountService->findById($request->account_id));
        if ($request->type === 'transfer') {
            $this->authorize('view', $this->accountService->findById($request->transfer_to_account_id));
        }

        $this->transactionService->updateTransaction($transaction, $request->validated());
        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        $this->transactionService->deleteTransaction($transaction->id);
        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
    }
}
