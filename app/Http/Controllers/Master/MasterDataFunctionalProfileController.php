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

class MasterDataFunctionalProfileController extends Controller
{
    private PythonModuleMasterDataService $moduleMasterDataService;

    public function __construct(PythonModuleMasterDataService $moduleMasterDataService)
    {
        $this->moduleMasterDataService = $moduleMasterDataService;
    }

    /**
     * GET request for profile management page
     */
    public function functionalProfileManagementPage(Request $request): View
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('Computer access functional profile management page', ['userId' => $user?->id, 'userName' => $user?->name, 'route' => $request->route()->getName()]);

        return view('base-components.base-vue', [
            'title' => __('app.page.functional-profile-management'),
        ]);
    }

    /**
     * GET request for get profile list
     */
    public function getFuncProfileList(Request $request): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting get profile list', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip()]);

        /** Validate Input */
        $validate = Validator::make($request->all(), [
            'profile' => ['nullable', 'uuid'],
            'company' => ['nullable', 'uuid'],
            'division' => ['nullable', 'uuid'],
            'limit_code' => ['nullable', 'string'],
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
                'profile_id' => $validated['profile'] ?? null,
                'company_id' => $validated['company'] ?? null,
                'merch_struct_id' => $validated['division'] ?? null,
                'limit_code' => $validated['limit_code'] ?? null,
                'status' => $validated['status'] ?? null,
                'search' => $validated['search'] ?? null,
                'skip' => $validated['skip'] ?? null,
                'limit' => $validated['limit'] ?? null,
                'sort_by' => $validated['sort_by'] ?? null,
                'sort_order' => $validated['sort_order'] ?? null,
            ];
            $data = $this->moduleMasterDataService->getFuncProfileList($params);

            return response()->json($data);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }

    /**
     * GET request for get functional profile detail
     */
    public function getFuncProfileDetail(Request $request, $id): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting get functional profile detail', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip(), 'funcProfileId' => $id]);

        /** Validate ID parameter */
        $validate = Validator::make(['id' => $id], [
            'id' => ['required', 'uuid'],
        ]);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }
        try {
            $data = $this->moduleMasterDataService->getFuncProfileDetail($id);

            return response()->json($data);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }

    /**
     * POST request for create functional profile
     */
    public function postFuncProfileCreate(Request $request): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting create functional profile', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip()]);

        /** Validate Input */
        $validate = Validator::make($request->all(), [
            'profile_id' => ['required', 'uuid', 'exists:App\Models\Master\Profile,id'],
            'company' => ['required', 'uuid', 'exists:App\Models\Master\Company,id'],
            'division' => ['required', 'array'],
            'division.*.id' => ['required', 'uuid'],
            'limit' => ['required', 'string', 'exists:App\Models\Master\Limit,code'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }
        (array) $validated = $validate->validated();

        try {
            $params = [
                'profile_id' => $validated['profile_id'],
                'company_id' => $validated['company'],
                'division' => $validated['division'] ?? [],
                'limit_code' => $validated['limit'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'user_id' => $user?->id,
            ];
            $data = $this->moduleMasterDataService->createFuncProfile($params);

            return response()->json($data, 201);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }

    /**
     * PUT request for update functional profile
     */
    public function postFuncProfileUpdate(Request $request, $id): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting update functional profile', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip(), 'funcProfileId' => $id]);

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
            'profile_id' => ['required', 'uuid', 'exists:App\Models\Master\Profile,id'],
            'company' => ['required', 'uuid', 'exists:App\Models\Master\Company,id'],
            'merch_struct' => ['required', 'uuid', 'exists:App\Models\Master\MerchStruct,id'],
            'limit' => ['required', 'string', 'exists:App\Models\Master\Limit,code'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }
        (array) $validated = $validate->validated();

        try {
            $params = [
                'profile_id' => $validated['profile_id'],
                'company_id' => $validated['company'],
                'merch_struct_id' => $validated['merch_struct'],
                'limit_code' => $validated['limit'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'user_id' => $user?->id,
            ];
            $data = $this->moduleMasterDataService->updateFuncProfile($idValidated['id'], $params);

            return response()->json($data);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }
}
