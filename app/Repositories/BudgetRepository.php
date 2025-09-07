<?php

namespace App\Repositories;

use App\Models\Budget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BudgetRepository
{

    public function getAllByUser()
    {
        return Budget::where('user_id', Auth::id())
            ->with('category')
            ->where('period', 'monthly')
            ->get();
    }

    public function create(array $data)
    {
        $data['user_id'] = Auth::id();
        $data['period'] = 'monthly';
        $data['start_date'] = now()->startOfMonth();

        return Budget::create($data);
    }

    // --- METHOD FOR AI ASSISTANT ---

    public function getActiveBudgetsForUser(int $userId)
    {
        $today = Carbon::today();
        
        return Budget::with('category')
            ->where('user_id', $userId)
            ->where('start_date', '<=', $today->endOfMonth())
            ->where('period', 'monthly')
            ->get();
    }
}
