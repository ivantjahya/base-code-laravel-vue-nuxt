<?php

namespace App\Services;

use App\Exceptions\CommonCustomException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PythonModuleMasterDataService
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
     * Get limit list from Python API
     */
    public function getLimitList(array $params = []): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(60)
                ->get("{$this->baseUrl}/limit/get_list", $params)
                ->throw(),
            '/masterdata/limit/get_list',
            'Failed to get limit list',
            ['params' => $params]
        );
    }

    /**
     * Get limit detail by ID from Python API
     */
    public function getLimitDetail(string $id): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(60)
                ->get("{$this->baseUrl}/limit/{$id}")
                ->throw(),
            "/masterdata/limit/{$id}",
            'Failed to get limit detail',
            ['limit_id' => $id]
        );
    }

    /**
     * Create new limit in Python API
     */
    public function createLimit(array $data): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(30)
                ->post("{$this->baseUrl}/limit/post_create", $data)
                ->throw(),
            '/masterdata/limit/post_create',
            'Failed to create limit',
            ['request_data' => $data]
        );
    }

    /**
     * Update limit in Python API
     */
    public function updateLimit(string $id, array $data): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(30)
                ->put("{$this->baseUrl}/limit/post_update/{$id}", $data)
                ->throw(),
            "/masterdata/limit/post_update/{$id}",
            'Failed to update limit',
            ['limit_id' => $id, 'request_data' => $data]
        );
    }

    /**
     * Extend limit in Python API
     */
    public function extendLimit(string $id, array $data): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(30)
                ->put("{$this->baseUrl}/limit/post_extend/{$id}", $data)
                ->throw(),
            "/masterdata/limit/post_extend/{$id}",
            'Failed to extend limit',
            ['limit_id' => $id, 'request_data' => $data]
        );
    }
}
