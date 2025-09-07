<?php

namespace App\Repositories;

use App\Models\RecurringTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RecurringTransactionRepository
{
    public function getAllByUser(): Collection
    {
        return RecurringTransaction::where('user_id', Auth::id())
            ->with(['account', 'category'])
            ->latest()
            ->get();
    }

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
