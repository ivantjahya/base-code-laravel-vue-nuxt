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

class MasterDataApprovalFlowController extends Controller
{
    private PythonModuleMasterDataService $moduleMasterDataService;

    public function __construct(PythonModuleMasterDataService $moduleMasterDataService)
    {
        $this->moduleMasterDataService = $moduleMasterDataService;
    }

    /**
     * GET request for approval flow management page
     */
    public function approvalFlowManagementPage(Request $request): View
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('Computer access approval flow management page', ['userId' => $user?->id, 'userName' => $user?->name, 'route' => $request->route()->getName()]);

        return view('base-components.base-vue', [
            'title' => __('app.page.approval-flow-management'),
        ]);
    }

    /**
     * GET request for get approval flow list
     */
    public function getApprovalFlowList(Request $request)
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting get approval flow list', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip()]);

        /** Validate Input */
        $validate = Validator::make($request->all(), [
            'code' => ['nullable', 'string'],
            'name' => ['nullable', 'string'],
            'profile' => ['nullable', 'uuid'],
            'division' => ['nullable', 'uuid'],
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
                'code' => $validated['code'] ?? null,
                'name' => $validated['name'] ?? null,
                'profile_id' => $validated['profile'] ?? null,
                'merch_struct_id' => $validated['division'] ?? null,
                'status' => $validated['status'] ?? null,
                'search' => $validated['search'] ?? null,
                'skip' => $validated['skip'] ?? null,
                'limit' => $validated['limit'] ?? null,
                'sort_by' => $validated['sort_by'] ?? null,
                'sort_order' => $validated['sort_order'] ?? null,
            ];
            Log::debug('approval flow with parameters', ['params' => $params]);
            $data = $this->moduleMasterDataService->getApprovalFlowList($params);

            return response()->json($data);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }

    /**
     * GET request for get approval flow detail
     */
    public function getApprovalFlowDetail(Request $request, $id): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting get approval flow detail', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip(), 'approvalFlowId' => $id]);

        /** Validate ID parameter */
        $validate = Validator::make(['id' => $id], [
            'id' => ['required', 'uuid'],
        ]);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }
        try {
            $data = $this->moduleMasterDataService->getApprovalFlowDetail($id);

            return response()->json($data);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }

    /**
     * POST request for create approval flow
     */
    public function postApprovalFlowCreate(Request $request): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting create approval flow', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip()]);

        /** Validate Input */
        $validate = Validator::make($request->all(), [
            'profile' => ['required', 'uuid', 'exists:App\Models\Master\Profile,id'],
            'division' => ['required', 'uuid', 'exists:App\Models\Master\MerchStruct,id'],
            'poStatus' => ['required', 'uuid', 'exists:App\Models\Master\status,id'],
            'request_to' => ['required', 'uuid', 'exists:App\Models\Master\Profile,id'],
            'nextPoStatus' => ['required', 'uuid', 'exists:App\Models\Master\status,id'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'integer', 'in:0,1'],
        ]);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }
        (array) $validated = $validate->validated();

        try {
            $params = [
                'profile_id' => $validated['profile'],
                'merch_struct_id' => $validated['division'],
                'po_status_id' => $validated['poStatus'],
                'next_profile_id' => $validated['request_to'],
                'next_po_status_id' => $validated['nextPoStatus'],
                'name' => 'TES',
                'status' => (int) $validated['status'],
                'user_id' => $user?->id,
            ];
            $data = $this->moduleMasterDataService->createApprovalFlow($params);

            return response()->json($data, 201);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }

    public function postApprovalFlowUpdate(Request $request, $id): HttpJsonResponse
    {

        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting update approval flow', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip(), 'funcProfileId' => $id]);

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
            'profile' => ['required', 'uuid', 'exists:App\Models\Master\Profile,id'],
            'division' => ['required', 'uuid', 'exists:App\Models\Master\MerchStruct,id'],
            'poStatus' => ['required', 'uuid', 'exists:App\Models\Master\Status,id'],
            'request_to' => ['required', 'uuid', 'exists:App\Models\Master\MerchStruct,id'],
            'nextPoStatus' => ['required', 'uuid', 'exists:App\Models\Master\Status,id'],
            'description' => ['required', 'string'],
            'status' => ['required', 'integer', 'in:0,1'],
        ]);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }
        (array) $validated = $validate->validated();

        try {
            $params = [
                'name' => $validated['description'],
                'profile_id' => $validated['profile'],
                'po_status_id' => $validated['poStatus'],
                'merch_struct_id' => $validated['division'],
                'next_profile_id' => $validated['requestTo'],
                'next_po_status_id' => $validated['nextPoStatus'],
                'status' => (int) $validated['status'],
                'user_id' => $user?->id,
            ];
            $data = $this->moduleMasterDataService->updateApprovalFlow($idValidated['id'], $params);

            return response()->json($data);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }

    }
}
