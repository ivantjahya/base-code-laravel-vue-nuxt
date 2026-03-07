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

class MasterDataUserController extends Controller
{
    private PythonModuleMasterDataService $moduleMasterDataService;

    public function __construct(PythonModuleMasterDataService $moduleMasterDataService)
    {
        $this->moduleMasterDataService = $moduleMasterDataService;
    }

    /**
     * GET request for user management page
     */
    public function userManagementPage(Request $request): View
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('Computer access user management page', ['userId' => $user?->id, 'userName' => $user?->name, 'route' => $request->route()->getName()]);

        return view('base-components.base-vue', [
            'title' => __('app.page.user-management'),
        ]);
    }

    /**
     * GET request for get user list
     */
    public function getUserList(Request $request): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting get user list', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip()]);

        /** Validate Input */
        $validate = Validator::make($request->all(), [
            'username' => ['nullable', 'string'],
            'name' => ['nullable', 'string'],
            'profile' => ['nullable', 'uuid'],
            'category' => ['nullable', 'uuid'],
            'status' => ['nullable', 'integer', 'in:0,1'],
            'skip' => ['nullable', 'integer', 'min:0'],
            'limit' => ['nullable', 'integer', 'min:1'],
            'search' => ['nullable', 'string'],
            'sort_by' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'string', 'in:asc,desc'],
        ]);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }
        (array) $validated = $validate->validated();

        try {
            $params = [
                'username' => $validated['username'] ?? null,
                'name' => $validated['name'] ?? null,
                'profile_id' => $validated['profile'] ?? null,
                'merch_struct_id' => $validated['category'] ?? null,
                'search' => $validated['search'] ?? null,
                'skip' => $validated['skip'] ?? null,
                'limit' => $validated['limit'] ?? null,
                'sort_by' => $validated['sort_by'] ?? null,
                'sort_order' => $validated['sort_order'] ?? null,
            ];
            $data = $this->moduleMasterDataService->getUserList($params);

            return response()->json($data);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }

    /**
     * GET request for get user detail
     */
    public function getUserDetail(Request $request, $id): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting get user detail', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip(), 'userId' => $id]);

        /** Validate ID parameter */
        $validate = Validator::make(['id' => $id], [
            'id' => ['required', 'uuid'],
        ]);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }
        try {
            $data = $this->moduleMasterDataService->getUserDetail($id);

            return response()->json($data);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }

    /**
     * POST request for create user
     */
    public function postUserCreate(Request $request): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting create user', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip()]);

        /** Validate Input */
        $validate = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string'],
            'profile_id' => ['required', 'uuid', 'exists:App\Models\Master\Profile,id'],
            'category' => ['required', 'array'],
            'site' => ['required', 'array'],
            'valid_date' => ['required', 'date'],
        ]);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }
        (array) $validated = $validate->validated();

        try {
            $params = [
                'username' => $validated['username'],
                'name' => $validated['name'],
                'password' => config('user.default_password_user'),
                'profile_id' => $validated['profile_id'],
                'merch_struct_ids' => $validated['category'],
                'site_ids' => $validated['site'],
                'valid_date' => $validated['valid_date'],
                'user_id' => $user?->id,
            ];
            $data = $this->moduleMasterDataService->createUser($params);

            return response()->json($data, 201);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }

    /**
     * PUT request for update user
     */
    public function postUserUpdate(Request $request, $id): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting update user', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip(), 'userId' => $id]);

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
            'name' => ['required', 'string'],
            'profile_id' => ['required', 'uuid', 'exists:App\Models\Master\Profile,id'],
            'category' => ['required', 'array'],
            'site' => ['required', 'array'],
            'valid_date' => ['required', 'date'],
        ]);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }
        (array) $validated = $validate->validated();

        try {
            $params = [
                'name' => $validated['name'],
                'profile_id' => $validated['profile_id'],
                'merch_struct_ids' => $validated['category'],
                'site_ids' => $validated['site'],
                'valid_date' => $validated['valid_date'],
                'user_id' => $user?->id,
            ];
            $data = $this->moduleMasterDataService->updateUser($idValidated['id'], $params);

            return response()->json($data);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }

    /**
     * PUT request for reset user password
     */
    public function postResetPassword(Request $request, $id): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting reset password', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip(), 'userId' => $id]);

        /** Validate ID parameter */
        $idValidate = Validator::make(['id' => $id], [
            'id' => ['required', 'uuid'],
        ]);
        if ($idValidate->fails()) {
            throw new ValidationException($idValidate);
        }
        (array) $idValidated = $idValidate->validated();

        try {
            $params = [
                'password' => config('user.reset_password'),
                'user_id' => $user?->id,
            ];
            $data = $this->moduleMasterDataService->resetPassword($idValidated['id'], $params);

            return response()->json($data);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }

    /**
     * PUT request for unlock user
     */
    public function postUnlockUser(Request $request, $id): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting unlock user', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip(), 'userId' => $id]);

        /** Validate ID parameter */
        $idValidate = Validator::make(['id' => $id], [
            'id' => ['required', 'uuid'],
        ]);
        if ($idValidate->fails()) {
            throw new ValidationException($idValidate);
        }
        (array) $idValidated = $idValidate->validated();

        try {
            $params = [
                'user_id' => $user?->id,
            ];
            $data = $this->moduleMasterDataService->unlockUser($idValidated['id'], $params);

            return response()->json($data);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }
}
