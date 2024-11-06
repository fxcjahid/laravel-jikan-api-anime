<?php

namespace App\Services;

use App\Exceptions\JikanApiException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class JikanApiService
{
    private const BASE_URL       = 'https://api.jikan.moe/v4';
    private const RATE_LIMIT_KEY = 'jikan_api_last_request';
    private const REQUEST_DELAY  = 1; // seconds between requests

    public function getTopAnime(int $page = 1, int $limit = 25)
    {
        $this->enforceRateLimit();

        try {
            $response = Http::timeout(10)
                ->get(self::BASE_URL . '/top/anime', [
                    'page'  => $page,
                    'limit' => $limit,
                ]);

            if ($response->successful()) {
                return $response->json()['data'];
            }

            $this->handleErrorResponse($response);
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    private function enforceRateLimit() : void
    {
        $lastRequest = Cache::get(self::RATE_LIMIT_KEY);

        if ($lastRequest) {
            $timeSinceLastRequest = time() - $lastRequest;
            if ($timeSinceLastRequest < self::REQUEST_DELAY) {
                usleep((self::REQUEST_DELAY - $timeSinceLastRequest) * 1000000);
            }
        }

        Cache::put(self::RATE_LIMIT_KEY, time(), now()->addMinutes(5));
    }

    private function handleErrorResponse($response) : void
    {
        $statusCode = $response->status();
        $body       = $response->json();
        $message    = $body['message'] ?? 'Unknown API error';

        Log::error('Jikan API Error', [
            'status'   => $statusCode,
            'message'  => $message,
            'response' => $body,
        ]);

        throw new JikanApiException($message, $statusCode);
    }

    private function handleException(\Exception $e) : void
    {
        Log::error('Jikan API Exception', [
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
        ]);

        throw new JikanApiException(
            'Failed to communicate with Jikan API: ' . $e->getMessage(),
            500,
            $e,
        );
    }
}
