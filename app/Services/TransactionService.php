<?php
// File: app/Services/TransactionService.php

namespace App\Services;

use App\Repositories\TransactionRepository;
use App\Repositories\AccountRepository;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TransactionService
{
    protected $transactionRepository;
    protected $accountRepository;
    protected $goalService; // Tambahkan properti untuk GoalService

    public function __construct(
        TransactionRepository $transactionRepository,
        AccountRepository $accountRepository,
        GoalService $goalService // Inject GoalService
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->accountRepository = $accountRepository;
        $this->goalService = $goalService; // Inisialisasi GoalService
    }

    /**
     * Get paginated transactions for the authenticated user.
     */
    public function getUserTransactionsPaginated(int $perPage = 15)
    {
        // Eager load 'goal' relationship for display in the view
        // Pastikan ini memanggil metode paginated dari repository
        return $this->transactionRepository->getAllByUserPaginated($perPage); // .load('goal') sudah ada di repository sekarang
    }

    /**
     * Create a new transaction and update the associated account balances and goal amount.
     */
    public function createTransaction(array $data)
    {
        return DB::transaction(function () use ($data) {
            $account = $this->accountRepository->findById($data['account_id']);
            $this->applyTransactionToAccount($account, $data['amount'], $data['type']);

            if ($data['type'] === 'transfer') {
                $destinationAccount = $this->accountRepository->findById($data['transfer_to_account_id']);
                $this->applyTransactionToAccount($destinationAccount, $data['amount'], 'income'); // Transfer is income for dest account
            }

            // Jika transaksi adalah pendapatan dan dikaitkan dengan tujuan
            if ($data['type'] === 'income' && isset($data['goal_id']) && !is_null($data['goal_id'])) {
                $goal = $this->goalService->findById((int)$data['goal_id']);
                $this->goalService->increaseGoalCurrentAmount($goal, $data['amount']);
            }
            
            return $this->transactionRepository->create($data);
        });
    }

    /**
     * Update an existing transaction and correct related account balances and goal amount.
     */
    public function updateTransaction(Transaction $transaction, array $newData)
    {
        return DB::transaction(function () use ($transaction, $newData) {
            // 1. Revert the effects of the old transaction
            $oldAccount = $this->accountRepository->findById($transaction->account_id);
            $this->revertTransactionFromAccount($oldAccount, $transaction->amount, $transaction->type);
            if ($transaction->type === 'transfer') {
                $oldDestinationAccount = $this->accountRepository->findById($transaction->transfer_to_account_id);
                $this->revertTransactionFromAccount($oldDestinationAccount, $transaction->amount, 'income');
            }

            // Revert old goal amount if it was an income transaction linked to a goal
            if ($transaction->type === 'income' && !is_null($transaction->goal_id)) {
                $oldGoal = $this->goalService->findById($transaction->goal_id);
                $this->goalService->decreaseGoalCurrentAmount($oldGoal, $transaction->amount);
            }

            // 2. Apply the effects of the new transaction
            $newAccount = $this->accountRepository->findById($newData['account_id']);
            $this->applyTransactionToAccount($newAccount, $newData['amount'], $newData['type']);
            if ($newData['type'] === 'transfer') {
                $newDestinationAccount = $this->accountRepository->findById($newData['transfer_to_account_id']);
                $this->applyTransactionToAccount($newDestinationAccount, $newData['amount'], 'income');
            }

            // Apply new goal amount if it's an income transaction linked to a goal
            if ($newData['type'] === 'income' && isset($newData['goal_id']) && !is_null($newData['goal_id'])) {
                $newGoal = $this->goalService->findById((int)$newData['goal_id']);
                $this->goalService->increaseGoalCurrentAmount($newGoal, $newData['amount']);
            }
            // If the transaction type changed from income to something else, or goal_id was removed
            // The old goal amount has already been reverted. Nothing additional needed here.


            // 3. Update the transaction data in the database
            return $this->transactionRepository->update($transaction->id, $newData);
        });
    }

    /**
     * Delete a transaction and revert the associated account balance changes and goal amount.
     */
    public function deleteTransaction(int $id)
    {
        return DB::transaction(function () use ($id) {
            $transaction = $this->transactionRepository->findById($id);
            $account = $this->accountRepository->findById($transaction->account_id);
            $this->revertTransactionFromAccount($account, $transaction->amount, $transaction->type);

            if ($transaction->type === 'transfer') {
                $destinationAccount = $this->accountRepository->findById($transaction->transfer_to_account_id);
                $this->revertTransactionFromAccount($destinationAccount, $transaction->amount, 'income'); // Revert as income
            }

            // Jika transaksi yang dihapus adalah pendapatan dan dikaitkan dengan tujuan
            if ($transaction->type === 'income' && !is_null($transaction->goal_id)) {
                $goal = $this->goalService->findById($transaction->goal_id);
                $this->goalService->decreaseGoalCurrentAmount($goal, $transaction->amount);
            }

            return $this->transactionRepository->delete($id);
        });
    }

    /**
     * Apply transaction amount to account balance.
     */
    protected function applyTransactionToAccount($account, $amount, $type)
    {
        if ($type === 'income') {
            $account->balance += $amount;
        } elseif ($type === 'expense' || $type === 'transfer') {
            $account->balance -= $amount;
        }
        $account->save();
    }

    /**
     * Revert transaction amount from account balance.
     */
    protected function revertTransactionFromAccount($account, $amount, $type)
    {
        if ($type === 'income') {
            $account->balance -= $amount;
        } elseif ($type === 'expense' || $type === 'transfer') {
            $account->balance += $amount;
        }
        $account->save();
    }
}
