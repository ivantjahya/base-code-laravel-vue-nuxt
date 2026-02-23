<?php

namespace App\Http\Controllers\Master;

use App\Exceptions\CommonCustomException;
use App\Http\Controllers\Controller;
use App\Services\PythonModuleMasterDataService;
use Illuminate\Http\JsonResponse as HttpJsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MasterDataProfileController extends Controller
{
    private PythonModuleMasterDataService $moduleMasterDataService;

    public function __construct(PythonModuleMasterDataService $moduleMasterDataService)
    {
        $this->moduleMasterDataService = $moduleMasterDataService;
    }

    /**
     * GET request for profile management page
     */
    public function profileManagementPage(Request $request): View
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('Computer access profile management page', ['userId' => $user?->id, 'userName' => $user?->name, 'route' => $request->route()->getName()]);

        return view('base-components.base-vue', [
            'title' => __('app.page.profile-management'),
        ]);
    }

    /**
     * GET request for get profile list
     */
    public function getProfileList(Request $request): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting get profile list', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip()]);

        /** Validate Input */
        $validate = Validator::make($request->all(), [
            'profile_code' => ['nullable', 'string'],
            'profile_name' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'profile_source' => ['nullable', 'integer', 'in:0,1'],
            'status' => ['nullable', 'integer', 'in:0,1'],
            'skip' => ['nullable', 'integer', 'min:0'],
            'limit' => ['nullable', 'integer', 'min:1'],
            'search' => ['nullable', 'string'],
            'sort_by' => ['nullable', 'string'],
        ]);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }
        (array) $validated = $validate->validated();

        try {
            $params = [
                'code' => $validated['profile_code'] ?? null,
                'name' => $validated['profile_name'] ?? null,
                'description' => $validated['description'] ?? null,
                'is_internal' => $validated['profile_source'] ?? null,
                'status' => $validated['status'] ?? null,
                'search' => $validated['search'] ?? null,
                'skip' => $validated['skip'] ?? null,
                'limit' => $validated['limit'] ?? null,
                'sort_by' => $validated['sort_by'] ?? null,
            ];
            $data = $this->moduleMasterDataService->getProfileList($params);

            return response()->json($data);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }

    /**
     * GET request for get profile detail
     */
    public function getProfileDetail(Request $request, $id): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting get profile detail', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip(), 'profileId' => $id]);

        /** Validate ID parameter */
        $validate = Validator::make(['id' => $id], [
            'id' => ['required', 'uuid'],
        ]);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }
        try {
            $data = $this->moduleMasterDataService->getProfileDetail($id);

            return response()->json($data);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }

    /**
     * POST request for create profile
     */
    public function postProfileCreate(Request $request): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting create profile', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip()]);

        /** Validate Input */
        $validate = Validator::make($request->all(), [
            'profile_name' => ['required', 'string'],
            'profile_description' => ['nullable', 'string'],
            'profile_source' => ['required', 'integer', 'in:0,1'],
            'menu_access' => ['nullable', 'array'],
            'menu_access.*.acc_control_id' => ['nullable', 'uuid'],
            'menu_access.*.menu_id' => ['required', 'uuid'],
            'status' => ['required', 'integer', 'in:0,1'],
        ]);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }
        (array) $validated = $validate->validated();

        try {
            $params = [
                'name' => $validated['profile_name'],
                'description' => $validated['profile_description'],
                'is_internal' => (int) $validated['profile_source'],
                'status' => (int) $validated['status'],
                'menu_access' => $validated['menu_access'] ?? [], // Default empty menu access, can be updated later in profile detail
                'user_id' => $user?->id,
            ];
            $data = $this->moduleMasterDataService->createProfile($params);

            return response()->json($data, 201);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }

    /**
     * PUT request for update profile
     */
    public function postProfileUpdate(Request $request, $id): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting update profile', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip(), 'profileId' => $id]);

        /** Validate ID parameter */
        $idValidate = Validator::make(['id' => $id], [
            'id' => ['required', 'uuid'],
        ]);
        if ($idValidate->fails()) {
            throw new ValidationException($idValidate);
        }
        (array) $idValidated = $idValidate->validated();

        /** Validate Input */
        $validate = Validator::make($request->all(), [
            'profile_name' => ['required', 'string'],
            'profile_description' => ['nullable', 'string'],
            'profile_source' => ['required', 'integer', 'in:0,1'],
            'menu_access' => ['nullable', 'array'],
            'menu_access.*.acc_control_id' => ['nullable', 'uuid'],
            'menu_access.*.menu_id' => ['required', 'uuid'],
            'status' => ['required', 'integer', 'in:0,1'],
        ]);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }
        (array) $validated = $validate->validated();

        try {
            $params = [
                'name' => $validated['profile_name'],
                'description' => $validated['profile_description'],
                'is_internal' => $validated['profile_source'],
                'status' => $validated['status'],
                'menu_access' => $validated['menu_access'] ?? [], // Default empty menu access, can be updated later in profile detail
                'user_id' => $user?->id,
            ];
            $data = $this->moduleMasterDataService->updateProfile($idValidated['id'], $params);

            return response()->json($data);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }
}
