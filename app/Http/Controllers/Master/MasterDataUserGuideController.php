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

class MasterDataUserGuideController extends Controller
{
    private PythonModuleMasterDataService $moduleMasterDataService;

    public function __construct(PythonModuleMasterDataService $moduleMasterDataService)
    {
        $this->moduleMasterDataService = $moduleMasterDataService;
    }

    /**
     * GET request for user guide management page
     */
    public function userGuideManagementPage(Request $request): View
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('Computer access user management page', ['userId' => $user?->id, 'userName' => $user?->name, 'route' => $request->route()->getName()]);

        return view('base-components.base-vue', [
            'title' => __('app.page.user-guide-management'),
        ]);
    }

    /**
     * GET request for get user guide list
     */
    public function getUserGuideList(Request $request)
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting get user guide list', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip()]);

        /** Validate Input */
        $validate = Validator::make($request->all(), [
            'user_guide_name' => ['nullable', 'string'],
            'user_guide_menu' => ['nullable', 'uuid'],
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
                'name' => $validated['user_guide_name'] ?? null,
                // 'menu_id' => $validated['user_guide_menu'] ?? null,
                'status' => $validated['status'] ?? null,
                'search' => $validated['search'] ?? null,
                'skip' => $validated['skip'] ?? null,
                'limit' => $validated['limit'] ?? null,
                'sort_by' => $validated['sort_by'] ?? null,
                'sort_order' => $validated['sort_order'] ?? null,
            ];
            Log::debug('user guide flow with parameters', ['params' => $params]);
            $data = $this->moduleMasterDataService->getUserGuideList($params);

            return response()->json($data);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }

    /**
     * GET request for get user guide detail
     */
    public function getUserGuideDetail(Request $request, $id): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting get user guide detail', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip(), 'userGuideId' => $id]);

        /** Validate ID parameter */
        $validate = Validator::make(['id' => $id], [
            'id' => ['required', 'uuid'],
        ]);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }
        try {
            $data = $this->moduleMasterDataService->getUserGuideDetail($id);

            return response()->json($data);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }

    /**
     * POST request for create user guide
     */
    public function postUserGuideCreate(Request $request): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting create user guide', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip()]);

        /** Validate Input */
        $validate = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'menu' => ['required', 'uuid', 'exists:App\Models\Master\Menu,id'],
            'status' => ['required', 'integer', 'in:0,1'],
        ]);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }
        (array) $validated = $validate->validated();

        try {
            $params = [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? '',
                'menu_id' => $validated['menu'],
                'file_name' => 'test.pdf',
                'file_path' => '/path/to/test.pdf',
                'status' => (int) $validated['status'],
                'user_id' => $user?->id,
            ];
            $data = $this->moduleMasterDataService->createUserGuide($params);

            return response()->json($data, 201);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }

    public function postUserGuideUpdate(Request $request, $id): HttpJsonResponse
    {

        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting update user guide', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip(), 'userGuideId' => $id]);

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
            'name' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'menu' => ['required', 'uuid', 'exists:App\Models\Master\Menu,id'],
            'status' => ['required', 'integer', 'in:0,1'],
        ]);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }
        (array) $validated = $validate->validated();

        try {
            $params = [
                'name' => $validated['name'],
                'description' => $validated['description'],
                'menu_id' => $validated['menu'],
                'file_name' => 'test.pdf',
                'file_path' => '/path/to/test.pdf',
                'status' => (int) $validated['status'],
                'user_id' => $user?->id,
            ];
            $data = $this->moduleMasterDataService->updateUserGuide($idValidated['id'], $params);

            return response()->json($data);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }

    }
}
