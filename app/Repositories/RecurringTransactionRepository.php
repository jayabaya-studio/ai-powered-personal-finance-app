<?php

namespace App\Repositories;

use App\Models\RecurringTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RecurringTransactionRepository
{
    /**
     * Get all recurring transactions for the authenticated user.
     */
    public function getAllByUser(): Collection
    {
        return RecurringTransaction::where('user_id', Auth::id())
            ->with(['account', 'category'])
            ->latest()
            ->get();
    }

    /**
     * [NEW] Get all active recurring transactions that are due.
     */
    public function getDueTransactions(): Collection
    {
        return RecurringTransaction::where('is_active', true)
            ->whereDate('next_due_date', '<=', now())
            ->get();
    }

    public function create(array $data): RecurringTransaction
    {
        $data['user_id'] = Auth::id();
        return RecurringTransaction::create($data);
    }

    public function findById(int $id): RecurringTransaction
    {
        return RecurringTransaction::where('user_id', Auth::id())->findOrFail($id);
    }

    public function update(int $id, array $data): bool
    {
        $transaction = $this->findById($id);
        return $transaction->update($data);
    }

    public function delete(int $id): bool
    {
        $transaction = $this->findById($id);
        return $transaction->delete();
    }
}
