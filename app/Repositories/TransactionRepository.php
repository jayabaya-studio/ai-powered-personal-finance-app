<?php
namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionRepository
{
    public function getAllByUserPaginated(int $perPage = 15)
    {
        return Transaction::where('user_id', Auth::id())
            ->with(['category', 'account', 'transferToAccount', 'goal'])
            ->latest('transaction_date')
            ->paginate($perPage);
    }

    public function create(array $data): Transaction
    {
        $data['user_id'] = Auth::id();
        return Transaction::create($data);
    }

    public function findById(int $id): Transaction
    {
        return Transaction::where('user_id', Auth::id())->findOrFail($id);
    }

    public function update(int $id, array $data): Transaction
    {
        $transaction = $this->findById($id);
        $transaction->update($data);
        return $transaction;
    }

    public function delete(int $id): bool
    {
        $transaction = $this->findById($id);
        return $transaction->delete();
    }

    public function getTotalIncomeForPeriod(int $userId, Carbon $startDate, Carbon $endDate)
    {
        return Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');
    }

    public function getTotalExpenseForPeriod(int $userId, Carbon $startDate, Carbon $endDate)
    {
         return Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');
    }

    public function getRecentTransactions(int $userId, int $limit = 50)
    {
        return Transaction::with('category') // Eager load relasi kategori
            ->where('user_id', $userId)
            ->orderBy('transaction_date', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getSpentForBudget(int $userId, int $categoryId, Carbon $startDate, Carbon $endDate)
    {
        return Transaction::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [Carbon::parse($startDate), $endDate])
            ->sum('amount');
    }
}
