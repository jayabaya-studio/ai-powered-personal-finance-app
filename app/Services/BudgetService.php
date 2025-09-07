<?php

namespace App\Services;

use App\Repositories\BudgetRepository;
use App\Models\Transaction;
use App\Notifications\BudgetWarningNotification;
use Illuminate\Support\Facades\Auth;

class BudgetService
{
    protected $budgetRepository;

    public function __construct(BudgetRepository $budgetRepository)
    {
        $this->budgetRepository = $budgetRepository;
    }

    public function getUserBudgetsWithCalculation()
    {
        $budgets = $this->budgetRepository->getAllByUser();
        $user = Auth::user();

        foreach ($budgets as $budget) {
            $spent = Transaction::where('user_id', $user->id)
                ->where('category_id', $budget->category_id)
                ->where('type', 'expense')
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('amount');
            
            $budget->spent = $spent;
            $budget->remaining = $budget->amount - $spent;
            $progress = $budget->amount > 0 ? ($spent / $budget->amount) * 100 : 0;
            $budget->progress = $progress;

            if ($progress > 90) {
                $notificationExists = $user->notifications()
                    ->where('data->message', 'like', "%'{$budget->category->name}'%")
                    ->whereMonth('created_at', now()->month)
                    ->exists();

                if (!$notificationExists) {
                    $user->notify(new BudgetWarningNotification($budget->category->name));
                }
            }
        }

        return $budgets;
    }

    public function createBudget(array $data)
    {
        return $this->budgetRepository->create($data);
    }
}
