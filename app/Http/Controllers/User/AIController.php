<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AIFinancialAnalysisService;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    protected $aiService;

    public function __construct(AIFinancialAnalysisService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:500',
        ]);

        try {
            $answer = $this->aiService->getAnalysis($request->input('question'));

            $formattedAnswer = \Illuminate\Support\Str::markdown($answer);

            return response()->json(['answer' => $formattedAnswer]);

        } catch (\Exception $e) {
            Log::error('AI Analysis Service failed: ' . $e->getMessage());

            return response()->json([
                'error' => '⚠️ Sorry, the system is having problems. Please try again later.''
            ], 500);
        }
    }
}
