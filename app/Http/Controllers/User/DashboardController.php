<?php

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

        $monthlySummary = $this->dashboardService->getMonthlyFinancialSummary();
        $recentTransactions = $this->dashboardService->getRecentTransactions(5);
        $topSpendingCategories = $this->dashboardService->getTopSpendingCategories();
        $cashFlow = $this->dashboardService->getMonthlyCashFlow();

        $budgetProgress = $this->dashboardService->getBudgetProgress();
        $financialHealthMetrics = $this->dashboardService->getFinancialHealthMetrics();

        $defaultCard = $user->cards()->where('is_default', true)->first();
        if (!$defaultCard) {
            $defaultCard = $user->cards()->first();
        }

        $goals = $this->goalService->getUserGoalsWithProgress();

        return view('user.dashboard', [
            'totalBalance' => $totalBalance,
            'totalIncome' => $cashFlow['income'],
            'totalExpense' => $cashFlow['expense'],
            'goals' => $goals,
            'monthlySummary' => $monthlySummary,
            'recentTransactions' => $recentTransactions,
            'defaultCard' => $defaultCard,
            'topSpendingCategories' => $topSpendingCategories,
            'cashFlow' => $cashFlow,
            'budgetProgress' => $budgetProgress,
            'financialHealthMetrics' => $financialHealthMetrics,
        ]);
    }

    /**
     * Provides data for the 3D expense chart (no longer used in the new design, but kept).
     */
    public function expenseChartData()
    {
        return response()->json([]);
    }
}
