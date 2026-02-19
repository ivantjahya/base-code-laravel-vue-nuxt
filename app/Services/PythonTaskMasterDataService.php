<?php

namespace App\Services;

use App\Exceptions\CommonCustomException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PythonTaskMasterDataService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.python.masterdata_endpoint'), '/');
    }

    /**
     * Handle API request with unified exception handling
     */
    private function handleApiRequest(
        callable $requestCallback,
        string $endpoint,
        string $errorMessage,
        array $logContext = []
    ): array {
        try {
            $response = $requestCallback();

            return $response->json();

        } catch (ConnectionException $e) {
            // ⏱ Timeout / network issue
            Log::error('Python API timeout or connection failed', array_merge([
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ], $logContext));

            throw new CommonCustomException(
                'Python API timeout or connection failed',
                504,
                $e
            );

        } catch (RequestException $e) {
            // ❌ Python API returned 4xx / 5xx
            $response = $e->response;

            Log::error('Python API returned error response', array_merge([
                'endpoint' => $endpoint,
                'status' => $response?->status(),
                'body' => $response?->body(),
            ], $logContext));

            // FastAPI uses 'detail', Laravel typically uses 'message'
            $apiErrorMessage = $response?->json('detail')
                ?? $response?->json('message')
                ?? $errorMessage;

            throw new CommonCustomException(
                $apiErrorMessage,
                $response?->status() ?? 500,
                $e
            );

        } catch (\Throwable $e) {
            // 💥 Any other unexpected error
            Log::error('Unexpected error when calling Python API', array_merge([
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ], $logContext));

            throw new CommonCustomException(
                $errorMessage,
                500,
                $e
            );
        }
    }

    /**
     * Process data in middleware merchandise structure from Python API
     */
    public function processMiddlewareMerchStruct(): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(60)
                ->post("{$this->baseUrl}/merch_struct/process_middleware_ms/sync")
                ->throw(),
            '/merch_struct/process_middleware_ms/sync',
            'Failed to process middleware merchandise structure',
            []
        );
    }

    /**
     * Process data in middleware site gold from Python API
     */
    public function processMiddlewareSiteGold(): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(60)
                ->post("{$this->baseUrl}/regional_site/process_middleware_site_gold/sync")
                ->throw(),
            '/regional_site/process_middleware_site_gold/sync',
            'Failed to process middleware site gold',
            []
        );
    }
}
