<?php
// File: app/Services/DashboardService.php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Budget; // [BARU] Menambahkan model Budget
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * Get monthly financial summary for the last N months.
     *
     * @param int $monthsToShow
     * @return Collection
     */
    public function getMonthlyFinancialSummary(int $monthsToShow = 6): Collection
    {
        $userId = Auth::id();
        $results = collect();

        for ($i = $monthsToShow - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M');

            $income = Transaction::where('user_id', $userId)
                                 ->where('type', 'income')
                                 ->whereMonth('transaction_date', $date->month)
                                 ->whereYear('transaction_date', $date->year)
                                 ->sum('amount');

            $expense = Transaction::where('user_id', $userId)
                                  ->where('type', 'expense')
                                  ->whereMonth('transaction_date', $date->month)
                                  ->whereYear('transaction_date', $date->year)
                                  ->sum('amount');
            
            $results->push([
                'month' => $monthName,
                'income' => $income,
                'expense' => $expense,
            ]);
        }

        return $results;
    }

    /**
     * Get recent transactions for the user.
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecentTransactions(int $limit = 5): Collection
    {
        return Transaction::where('user_id', Auth::id())
            ->with('category')
            ->latest('transaction_date')
            ->limit($limit)
            ->get();
    }

    /**
     * [FIXED] Get the top spending categories for the current month.
     *
     * @param int $limit
     * @return Collection
     */
    public function getTopSpendingCategories(int $limit = 3): Collection
    {
        // [FIXED] Specify the table name for user_id to resolve ambiguity
        return Transaction::where('transactions.user_id', Auth::id())
            ->where('transactions.type', 'expense')
            ->whereMonth('transactions.transaction_date', now()->month)
            ->whereYear('transactions.transaction_date', now()->year)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select('categories.name as category_name', DB::raw('SUM(transactions.amount) as total'))
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    /**
     * Get the net cash flow for the current month.
     *
     * @return array
     */
    public function getMonthlyCashFlow(): array
    {
        $userId = Auth::id();

        $income = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        $expense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        return [
            'income' => $income,
            'expense' => $expense,
            'net_cash_flow' => $income - $expense,
        ];
    }

    // --- FUNGSI BARU UNTUK FINANCIAL INSIGHTS ---

    /**
     * [BARU] Mengambil progres budget bulanan dibandingkan dengan pengeluaran.
     *
     * @param int $limit
     * @return Collection
     */
    public function getBudgetProgress(int $limit = 4): Collection
    {
        $userId = Auth::id();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // 1. Ambil semua budget bulanan untuk pengguna saat ini
        $budgets = Budget::where('user_id', $userId)
            ->where('period', 'monthly')
            ->with('category') // Eager load relasi kategori
            ->get();

        // 2. Ambil semua total pengeluaran per kategori untuk bulan ini
        $expenses = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->groupBy('category_id')
            ->select('category_id', DB::raw('SUM(amount) as total_spent'))
            ->pluck('total_spent', 'category_id');

        // 3. Proses dan gabungkan data
        $budgetProgress = $budgets->map(function ($budget) use ($expenses) {
            $spent = $expenses->get($budget->category_id, 0);
            $budgetAmount = $budget->amount;
            $percentage = ($budgetAmount > 0) ? ($spent / $budgetAmount) * 100 : 0;

            return [
                'category_name' => $budget->category->name ?? 'Uncategorized',
                'budget_amount' => $budgetAmount,
                'spent_amount' => $spent,
                'percentage' => round($percentage),
                'remaining_amount' => $budgetAmount - $spent,
            ];
        });

        // Urutkan berdasarkan persentase tertinggi dan ambil sesuai limit
        return $budgetProgress->sortByDesc('percentage')->take($limit);
    }

    /**
     * [BARU] Menghitung metrik kesehatan finansial seperti Savings Rate.
     *
     * @return array
     */
    public function getFinancialHealthMetrics(): array
    {
        $cashFlow = $this->getMonthlyCashFlow();
        $income = $cashFlow['income'];
        $expense = $cashFlow['expense'];

        // Hitung Savings Rate: (Pemasukan - Pengeluaran) / Pemasukan
        // Hindari pembagian dengan nol jika tidak ada pemasukan
        $savingsRate = ($income > 0) ? (($income - $expense) / $income) * 100 : 0;

        return [
            'savings_rate' => round($savingsRate),
        ];
    }
}
