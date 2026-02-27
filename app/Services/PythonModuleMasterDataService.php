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

    /** ------------------------------------- MERCH STRUCT ------------------------------------- */
    /**
     * Get merch struct div cat list from Python API
     */
    public function getMerchStructDivCatList(): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(60)
                ->get("{$this->baseUrl}/merch_struct/get_merch_struct_div_cat")
                ->throw(),
            '/masterdata/merch_struct/get_merch_struct_div_cat',
            'Failed to get merch struct div cat list',
            []
        );
    }

    /** ------------------------------------- LIMIT ------------------------------------- */
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

    /** ------------------------------------- MENU ------------------------------------- */
    /**
     * Get menu list from Python API
     */
    public function getMenuList(): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(60)
                ->get("{$this->baseUrl}/menu/get_list")
                ->throw(),
            '/masterdata/menu/get_list',
            'Failed to get menu list',
            []
        );
    }

    /**
     * Get menu access control list from Python API
     */
    public function getMenuAccControlList(): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(60)
                ->get("{$this->baseUrl}/menu/get_menu_acc_controls")
                ->throw(),
            '/masterdata/menu/get_menu_acc_controls',
            'Failed to get menu access control list',
            []
        );
    }

    /** ------------------------------------- PROFILE ------------------------------------- */
    /**
     * Get profile list from Python API
     */
    public function getProfileList(array $params = []): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(60)
                ->get("{$this->baseUrl}/profile/get_list", $params)
                ->throw(),
            '/masterdata/profile/get_list',
            'Failed to get profile list',
            ['params' => $params]
        );
    }

    /**
     * Get profile detail by ID from Python API
     */
    public function getProfileDetail(string $id): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(60)
                ->get("{$this->baseUrl}/profile/{$id}")
                ->throw(),
            "/masterdata/profile/{$id}",
            'Failed to get profile detail',
            ['profile_id' => $id]
        );
    }

    /**
     * Create new profile in Python API
     */
    public function createProfile(array $data): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(30)
                ->post("{$this->baseUrl}/profile/post_create", $data)
                ->throw(),
            '/masterdata/profile/post_create',
            'Failed to create profile',
            ['request_data' => $data]
        );
    }

    /**
     * Update profile in Python API
     */
    public function updateProfile(string $id, array $data): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(30)
                ->put("{$this->baseUrl}/profile/post_update/{$id}", $data)
                ->throw(),
            "/masterdata/profile/post_update/{$id}",
            'Failed to update profile',
            ['profile_id' => $id, 'request_data' => $data]
        );
    }

    /** ------------------------------------- FUNCTIONAL PROFILE ------------------------------------- */
    /**
     * Get functional profile list from Python API
     */
    public function getFuncProfileList(array $params = []): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(60)
                ->get("{$this->baseUrl}/func_profile/get_list", $params)
                ->throw(),
            '/masterdata/func_profile/get_list',
            'Failed to get functional profile list',
            ['params' => $params]
        );
    }

    /**
     * Get functional profile detail by ID from Python API
     */
    public function getFuncProfileDetail(string $id): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(60)
                ->get("{$this->baseUrl}/func_profile/{$id}")
                ->throw(),
            "/masterdata/func_profile/{$id}",
            'Failed to get functional profile detail',
            ['func_profile_id' => $id]
        );
    }

    /**
     * Create new functional profile in Python API
     */
    public function createFuncProfile(array $data): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(30)
                ->post("{$this->baseUrl}/func_profile/post_create", $data)
                ->throw(),
            '/masterdata/func_profile/post_create',
            'Failed to create functional profile',
            ['request_data' => $data]
        );
    }

    /**
     * Update functional profile in Python API
     */
    public function updateFuncProfile(string $id, array $data): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(30)
                ->put("{$this->baseUrl}/func_profile/post_update/{$id}", $data)
                ->throw(),
            "/masterdata/func_profile/post_update/{$id}",
            'Failed to update functional profile',
            ['func_profile_id' => $id, 'request_data' => $data]
        );
    }

    /** ------------------------------------- REGIONAL SITE ------------------------------------- */
    /**
     * Get regional site list from Python API
     */
    public function getSiteList(array $params = []): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(60)
                ->get("{$this->baseUrl}/site/get_list", $params)
                ->throw(),
            '/masterdata/site/get_list',
            'Failed to get site list',
            ['params' => $params]
        );
    }

    /**
     * Get site detail by ID from Python API
     */
    // public function getSiteDetail(string $id): array
    // {
    //     return $this->handleApiRequest(
    //         fn () => Http::acceptJson()
    //             ->connectTimeout(3)
    //             ->timeout(60)
    //             ->get("{$this->baseUrl}/site/{$id}")
    //             ->throw(),
    //         "/masterdata/site/{$id}",
    //         'Failed to get site detail',
    //         ['site_id' => $id]
    //     );
    // }

    /** ------------------------------------- USER ------------------------------------- */
    /**
     * Get user list from Python API
     */
    public function getUserList(array $params = []): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(60)
                ->get("{$this->baseUrl}/user/get_list", $params)
                ->throw(),
            '/masterdata/user/get_list',
            'Failed to get user list',
            ['params' => $params]
        );
    }

    /** ------------------------------------- APPROVAL FLOW ------------------------------------- */
    /**
     * Get approval flow list from Python API
     */
    public function getApprovalFlowList(array $params = []): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(60)
                ->get("{$this->baseUrl}/approval_flow/get_list", $params)
                ->throw(),
            '/masterdata/approval_flow/get_list',
            'Failed to get approval flow list',
            ['params' => $params]
        );
    }

    /**
     * Create new approval flow in Python API
     */
    public function createApprovalFlow(array $data): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(30)
                ->post("{$this->baseUrl}/approval_flow/post_create", $data)
                ->throw(),
            '/masterdata/approval_flow/post_create',
            'Failed to create approval flow',
            ['request_data' => $data]
        );
    }

    /** ------------------------------------- USER GUIDE ------------------------------------- */
    /**
     * Get user guide list from Python API
     */
    public function getUserGuideList(array $params = []): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(60)
                ->get("{$this->baseUrl}/user_guide/get_list", $params)
                ->throw(),
            '/masterdata/user_guide/get_list',
            'Failed to get user guide list',
            ['params' => $params]
        );
    }

    /** ------------------------------------- STATUS PO ------------------------------------- */
    /**
     * Get status PO list from Python API
     */
    public function getPoStatusList(array $params = []): array
    {
        return $this->handleApiRequest(
            fn () => Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(60)
                ->get("{$this->baseUrl}/status/get_list_po_status", $params)
                ->throw(),
            '/masterdata/status/get_list_po_status',
            'Failed to get status PO list',
            ['params' => $params]
        );
    }


}
