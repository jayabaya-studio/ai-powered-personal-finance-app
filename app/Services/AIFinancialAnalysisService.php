<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Repositories\TransactionRepository;
use App\Repositories\BudgetRepository;
use App\Repositories\GoalRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AIFinancialAnalysisService
{
    protected GeminiService $geminiService;
    protected TransactionRepository $transactionRepository;
    protected BudgetRepository $budgetRepository;
    protected GoalRepository $goalRepository;

    public function __construct(
        GeminiService $geminiService,
        TransactionRepository $transactionRepository,
        BudgetRepository $budgetRepository,
        GoalRepository $goalRepository
    ) {
        $this->geminiService = $geminiService;
        $this->transactionRepository = $transactionRepository;
        $this->budgetRepository = $budgetRepository;
        $this->goalRepository = $goalRepository;
    }

    public function getAnalysis(string $question): string
    {
        $dateRange = $this->extractDateFromQuestion($question);

        $context = $this->gatherDataContext($dateRange['start_date'], $dateRange['end_date']);
        
        $prompt = $this->generatePrompt($context, $question);
        
        return $this->geminiService->generateContent($prompt);
    }

    private function extractDateFromQuestion(string $question): array
    {
        $currentDate = Carbon::now()->toDateString();
        
        $prompt = <<<PROMPT
        Analyze the following user question to identify a specific time period (a start date and an end date).
        - The current date is {$currentDate}.
        - If the user says "last August", and the current year is 2025, it means August 2024.
        - If the user says "this month", it refers to the current month.
        - If no specific time period is mentioned, default to the current month.
        - Your response MUST be a JSON object with "start_date" and "end_date" in "YYYY-MM-DD" format. Do NOT include any other text or explanation.

        User question: "{$question}"

        JSON Response:
        PROMPT;

        try {
            $response = $this->geminiService->generateContent($prompt);
            
            // Menggunakan regex untuk mengekstrak blok JSON pertama, lebih andal daripada substr/strpos
            $jsonResponse = '';
            if (preg_match('/\{.*\}/s', $response, $matches)) {
                $jsonResponse = $matches[0];
            }

            $decoded = json_decode($jsonResponse, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['start_date']) && isset($decoded['end_date'])) {
                return [
                    'start_date' => Carbon::parse($decoded['start_date']),
                    'end_date' => Carbon::parse($decoded['end_date']),
                ];
            }
        } catch (\Exception $e) {
            Log::error('Date extraction from question failed: ' . $e->getMessage());
        }

        return [
            'start_date' => Carbon::now()->startOfMonth(),
            'end_date' => Carbon::now()->endOfMonth(),
        ];
    }
    
    private function gatherDataContext(Carbon $startDate, Carbon $endDate): array
    {
        $user = Auth::user();

        $income = $this->transactionRepository->getTotalIncomeForPeriod($user->id, $startDate, $endDate);
        $expense = $this->transactionRepository->getTotalExpenseForPeriod($user->id, $startDate, $endDate);

        $recentTransactions = $this->transactionRepository->getRecentTransactions($user->id, 50);
        $formattedTransactions = $recentTransactions->map(function ($tx) {
            return [
                'date' => $tx->transaction_date->format('Y-m-d'),
                'description' => $tx->description,
                'category' => $tx->category->name ?? 'Uncategorized',
                'type' => $tx->type,
                'amount' => (float) $tx->amount,
            ];
        });
        
        $activeBudgets = $this->budgetRepository->getActiveBudgetsForUser($user->id);
        $formattedBudgets = $activeBudgets->map(function ($budget) use ($startDate, $endDate) {
            $spent = $this->transactionRepository->getSpentForBudget($budget->user_id, $budget->category_id, $startDate, $endDate);
            return [
                'category' => $budget->category->name,
                'budget_amount' => (float) $budget->amount,
                'spent_amount' => (float) $spent,
            ];
        });

        $activeGoals = $this->goalRepository->getActiveGoals($user->id);
        $formattedGoals = $activeGoals->map(function ($goal) {
            return ['name' => $goal->name, 'target_amount' => (float) $goal->target_amount, 'current_amount' => (float) $goal->current_amount];
        });


        return [
            'user_context' => [
                'currency' => 'USD',
                'analysis_period' => [
                    'from' => $startDate->toDateString(),
                    'to' => $endDate->toDateString(),
                ],
            ],
            'financial_summary' => [
                'total_income' => (float) $income,
                'total_expense' => (float) $expense,
            ],
            'recent_transactions' => $formattedTransactions->toArray(),
            'active_budgets' => $formattedBudgets->toArray(),
            'financial_goals' => $formattedGoals->toArray(),
        ];
    }

    private function generatePrompt(array $context, string $question): string
    {
        $jsonContext = json_encode($context, JSON_PRETTY_PRINT);

        return <<<PROMPT
        You are a professional, helpful, and insightful personal finance assistant.
        Your role is to analyze the user's financial data and answer their questions clearly and concisely.
        Do not provide absolute financial advice, but rather data-driven insights.
        **IMPORTANT: All financial amounts in the JSON data are in USD. Your final answer must also be in USD.**
        The analysis period is specified in the 'analysis_period' key in the JSON.

        Analyze the following JSON data which represents the user's financial state.

        Financial Data:
        {$jsonContext}

        Based on the data above, please answer the following question.
        Keep your answer friendly, easy to understand, and structured (e.g., use bullet points if necessary).

        User's Question: "{$question}"
        PROMPT;
    }
}
