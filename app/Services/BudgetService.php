<?php

namespace App\Services;

use App\Repositories\BudgetRepository;
use App\Models\Transaction;
use App\Notifications\BudgetWarningNotification; // [BARU] Import notifikasi
use Illuminate\Support\Facades\Auth;

class BudgetService
{
    protected $budgetRepository;

    public function __construct(BudgetRepository $budgetRepository)
    {
        $this->budgetRepository = $budgetRepository;
    }

    /**
     * Get user budgets with spending calculations and send notifications if needed.
     */
    public function getUserBudgetsWithCalculation()
    {
        $budgets = $this->budgetRepository->getAllByUser();
        $user = Auth::user();

        foreach ($budgets as $budget) {
            // Logika kalkulasi budget yang sudah ada tetap dipertahankan
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

            // [BARU] Logika untuk mengirim notifikasi
            // Kirim notifikasi jika progres > 90% dan belum pernah dikirim sebelumnya bulan ini
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

    /**
     * Create a new budget.
     */
    public function createBudget(array $data)
    {
        return $this->budgetRepository->create($data);
    }
}
