<?php
// File: app/Http/Controllers/User/DashboardController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\AccountService;
use App\Services\DashboardService;
use App\Services\GoalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $accountService;
    protected $dashboardService;
    protected $goalService;

    public function __construct(AccountService $accountService, DashboardService $dashboardService, GoalService $goalService)
    {
        $this->accountService = $accountService;
        $this->dashboardService = $dashboardService;
        $this->goalService = $goalService;
    }

    /**
     * Display the user's dashboard with dynamic data.
     */
    public function index()
    {
        $user = Auth::user();
        $accounts = $this->accountService->getAccountsForCurrentUser();
        $totalBalance = $accounts->sum('balance');

        // --- Mengambil semua data dari DashboardService ---
        $monthlySummary = $this->dashboardService->getMonthlyFinancialSummary();
        $recentTransactions = $this->dashboardService->getRecentTransactions(5);
        $topSpendingCategories = $this->dashboardService->getTopSpendingCategories();
        $cashFlow = $this->dashboardService->getMonthlyCashFlow();

        // [BARU] Mengambil data untuk indikator visual cerdas
        $budgetProgress = $this->dashboardService->getBudgetProgress();
        $financialHealthMetrics = $this->dashboardService->getFinancialHealthMetrics();


        // Mengambil kartu default pengguna
        $defaultCard = $user->cards()->where('is_default', true)->first();
        if (!$defaultCard) {
            $defaultCard = $user->cards()->first();
        }

        // Mendapatkan tujuan dengan progres
        $goals = $this->goalService->getUserGoalsWithProgress();

        return view('user.dashboard', [
            'totalBalance' => $totalBalance,
            'totalIncome' => $cashFlow['income'], // Menggunakan data dari cashFlow
            'totalExpense' => $cashFlow['expense'], // Menggunakan data dari cashFlow
            'goals' => $goals,
            'monthlySummary' => $monthlySummary,
            'recentTransactions' => $recentTransactions,
            'defaultCard' => $defaultCard,
            'topSpendingCategories' => $topSpendingCategories,
            'cashFlow' => $cashFlow,
            'budgetProgress' => $budgetProgress, // [BARU] Mengirim data progres budget
            'financialHealthMetrics' => $financialHealthMetrics, // [BARU] Mengirim data metrik kesehatan finansial
        ]);
    }

    /**
     * Provides data for the 3D expense chart (no longer used in the new design, but kept).
     */
    public function expenseChartData()
    {
        // Logika ini mungkin tidak lagi digunakan jika Anda menghapus chart lama.
        // Namun, kita biarkan saja untuk saat ini.
        // $data = $this->dashboardService->getExpenseByCategoryData();
        // return response()->json($data);
        return response()->json([]); // Mengembalikan data kosong untuk sementara
    }
}
