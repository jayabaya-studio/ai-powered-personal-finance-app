<?php
// File: app/Http/Controllers/User/TransactionController.php (Diperbarui)

namespace App\Http\Controllers\User; // Pastikan namespace benar dengan backslash

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\AccountService;
use App\Services\CategoryService;
use App\Services\TransactionService;
use App\Services\GoalService; // Impor GoalService
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tambahkan ini

class TransactionController extends Controller
{
    protected $transactionService;
    protected $accountService;
    protected $categoryService;
    protected $goalService; // Tambahkan properti untuk GoalService

    public function __construct(
        TransactionService $transactionService,
        AccountService $accountService,
        CategoryService $categoryService,
        GoalService $goalService // Inject GoalService
    ) {
        $this->transactionService = $transactionService;
        $this->accountService = $accountService;
        $this->categoryService = $categoryService;
        $this->goalService = $goalService; // Inisialisasi GoalService

        // Terapkan TransactionPolicy ke semua aksi resource secara default
        $this->authorizeResource(Transaction::class, 'transaction');
    }

    /**
     * Display a paginated list of the user's transactions.
     * Also prepares data for add/edit modals.
     */
    public function index(Request $request)
    {
        $transactions = $this->transactionService->getUserTransactionsPaginated();
        // Gunakan getAccountsForCurrentUser untuk mengambil akun pribadi dan bersama
        $accounts = $this->accountService->getAccountsForCurrentUser();
        $categories = $this->categoryService->getCategoriesForForm(); // Formatted for dropdown
        $goals = $this->goalService->getUserGoalsWithProgress(); // Dapatkan tujuan dengan progres

        $editTransaction = null;

        // Check if 'edit_id' is in the query string to open the edit modal
        if ($request->has('edit_id')) {
            try {
                // Policy 'view' sudah diaplikasikan oleh model binding di route,
                // tapi kita perlu menemukan transaksi ini untuk mengisi form.
                // Jika transaksi ditemukan dan diotorisasi, akan lolos.
                $editTransaction = $this->transactionService->findById((int)$request->edit_id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return redirect()->route('transactions.index')->with('error', 'Transaction not found or unauthorized.');
            }
        }
        
        return view('user.transactions.index', compact('transactions', 'accounts', 'categories', 'goals', 'editTransaction'));
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        // Policy 'create' sudah diaplikasikan oleh authorizeResource di konstruktor.
        // Tambahkan otorisasi manual untuk akun_id dan transfer_to_account_id
        // (ini mungkin redundant jika sudah divalidasi oleh AccountPolicy, tapi amannya)
        $this->authorize('view', $this->accountService->findById($request->account_id));
        if ($request->type === 'transfer') {
            $this->authorize('view', $this->accountService->findById($request->transfer_to_account_id));
        }

        $this->transactionService->createTransaction($request->validated());
        return redirect()->route('transactions.index')->with('success', 'Transaction added successfully.');
    }

    /**
     * Redirects to the index page with a parameter to open the edit modal.
     */
    public function edit(Transaction $transaction)
    {
        // Policy 'view' sudah diaplikasikan oleh authorizeResource di konstruktor.
        // Tidak perlu Auth::id() check manual lagi.
        return redirect()->route('transactions.index', ['edit_id' => $transaction->id]);
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        // Policy 'update' sudah diaplikasikan oleh authorizeResource di konstruktor.
        // Tambahkan otorisasi manual untuk akun_id dan transfer_to_account_id
        $this->authorize('view', $this->accountService->findById($request->account_id));
        if ($request->type === 'transfer') {
            $this->authorize('view', $this->accountService->findById($request->transfer_to_account_id));
        }

        $this->transactionService->updateTransaction($transaction, $request->validated());
        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
    }

    /**
     * Remove the specified transaction from storage.
     */
    public function destroy(Transaction $transaction)
    {
        // Policy 'delete' sudah diaplikasikan oleh authorizeResource di konstruktor.
        $this->transactionService->deleteTransaction($transaction->id);
        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
    }
}
