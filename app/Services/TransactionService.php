<?php

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
    protected $goalService;

    public function __construct(
        TransactionRepository $transactionRepository,
        AccountRepository $accountRepository,
        GoalService $goalService
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->accountRepository = $accountRepository;
        $this->goalService = $goalService;
    }

    public function getUserTransactionsPaginated(int $perPage = 15)
    {
        return $this->transactionRepository->getAllByUserPaginated($perPage);
    }

    public function createTransaction(array $data)
    {
        return DB::transaction(function () use ($data) {
            $account = $this->accountRepository->findById($data['account_id']);
            $this->applyTransactionToAccount($account, $data['amount'], $data['type']);

            if ($data['type'] === 'transfer') {
                $destinationAccount = $this->accountRepository->findById($data['transfer_to_account_id']);
                $this->applyTransactionToAccount($destinationAccount, $data['amount'], 'income');
            }

            if ($data['type'] === 'income' && isset($data['goal_id']) && !is_null($data['goal_id'])) {
                $goal = $this->goalService->findById((int)$data['goal_id']);
                $this->goalService->increaseGoalCurrentAmount($goal, $data['amount']);
            }
            
            return $this->transactionRepository->create($data);
        });
    }

    public function updateTransaction(Transaction $transaction, array $newData)
    {
        return DB::transaction(function () use ($transaction, $newData) {
            $oldAccount = $this->accountRepository->findById($transaction->account_id);
            $this->revertTransactionFromAccount($oldAccount, $transaction->amount, $transaction->type);
            if ($transaction->type === 'transfer') {
                $oldDestinationAccount = $this->accountRepository->findById($transaction->transfer_to_account_id);
                $this->revertTransactionFromAccount($oldDestinationAccount, $transaction->amount, 'income');
            }

            if ($transaction->type === 'income' && !is_null($transaction->goal_id)) {
                $oldGoal = $this->goalService->findById($transaction->goal_id);
                $this->goalService->decreaseGoalCurrentAmount($oldGoal, $transaction->amount);
            }

            $newAccount = $this->accountRepository->findById($newData['account_id']);
            $this->applyTransactionToAccount($newAccount, $newData['amount'], $newData['type']);
            if ($newData['type'] === 'transfer') {
                $newDestinationAccount = $this->accountRepository->findById($newData['transfer_to_account_id']);
                $this->applyTransactionToAccount($newDestinationAccount, $newData['amount'], 'income');
            }

            if ($newData['type'] === 'income' && isset($newData['goal_id']) && !is_null($newData['goal_id'])) {
                $newGoal = $this->goalService->findById((int)$newData['goal_id']);
                $this->goalService->increaseGoalCurrentAmount($newGoal, $newData['amount']);
            }
            return $this->transactionRepository->update($transaction->id, $newData);
        });
    }

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

            if ($transaction->type === 'income' && !is_null($transaction->goal_id)) {
                $goal = $this->goalService->findById($transaction->goal_id);
                $this->goalService->decreaseGoalCurrentAmount($goal, $transaction->amount);
            }

            return $this->transactionRepository->delete($id);
        });
    }

    protected function applyTransactionToAccount($account, $amount, $type)
    {
        if ($type === 'income') {
            $account->balance += $amount;
        } elseif ($type === 'expense' || $type === 'transfer') {
            $account->balance -= $amount;
        }
        $account->save();
    }

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
