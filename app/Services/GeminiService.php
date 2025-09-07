<?php

namespace App\Services;

use Gemini\Client;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiService
{
    protected Client $geminiClient;

    public function __construct(Client $geminiClient)
    {
        $this->geminiClient = $geminiClient;
    }

    public function generateContent(string $prompt): string
    {
        try {
            $result = $this->geminiClient
                ->generativeModel('gemini-1.5-flash-latest')
                ->generateContent($prompt);

            return $result->text();

        } catch (Exception $e) {
            Log::error('Gemini API Error: ' . $e->getMessage());
            return "Sorry, there was an issue communicating with the AI service. Please try again later.";
        }
    }
}

