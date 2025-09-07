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

            // Konversi jawaban (yang mungkin dalam format Markdown) ke HTML
            // agar bisa dirender dengan benar oleh x-html di frontend.
            $formattedAnswer = \Illuminate\Support\Str::markdown($answer);

            return response()->json(['answer' => $formattedAnswer]);

        } catch (\Exception $e) {
            Log::error('AI Analysis Service failed: ' . $e->getMessage());

            return response()->json([
                'error' => '⚠️ Maaf, sistem sedang bermasalah. Coba lagi nanti.'
            ], 500);
        }
    }
}
