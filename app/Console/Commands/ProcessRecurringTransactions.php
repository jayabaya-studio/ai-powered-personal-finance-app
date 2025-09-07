<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\RecurringTransactionRepository;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProcessRecurringTransactions extends Command
{
    protected $signature = 'app:process-recurring-transactions';

    protected $description = 'Process due recurring transactions and create standard transactions.';

    public function handle(RecurringTransactionRepository $recurringRepo, TransactionService $transactionService)
    {
        $this->info('Starting to process recurring transactions...');
        Log::info('Recurring transaction processing started.');

        $dueTransactions = $recurringRepo->getDueTransactions();

        if ($dueTransactions->isEmpty()) {
            $this->info('No due recurring transactions found.');
            Log::info('No due recurring transactions to process.');
            return;
        }

        $processedCount = 0;
        foreach ($dueTransactions as $recurring) {
            DB::transaction(function () use ($recurring, $transactionService, &$processedCount) {
                // 1. Create a new standard transaction
                $transactionService->createTransaction([
                    'user_id' => $recurring->user_id,
                    'account_id' => $recurring->account_id,
                    'category_id' => $recurring->category_id,
                    'type' => $recurring->type,
                    'amount' => $recurring->amount,
                    'description' => $recurring->description . ' (Recurring)',
                    'transaction_date' => $recurring->next_due_date,
                ]);

                // 2. Calculate and update the next due date
                $nextDate = Carbon::parse($recurring->next_due_date);
                switch ($recurring->frequency) {
                    case 'daily':
                        $nextDate->addDay();
                        break;
                    case 'weekly':
                        $nextDate->addWeek();
                        break;
                    case 'monthly':
                        $nextDate->addMonth();
                        break;
                    case 'yearly':
                        $nextDate->addYear();
                        break;
                }
                $recurring->next_due_date = $nextDate;
                $recurring->save();
                
                $processedCount++;
            });
        }

        $this->info("Successfully processed {$processedCount} recurring transactions.");
        Log::info("Recurring transaction processing finished. Processed: {$processedCount}.");
    }
}
