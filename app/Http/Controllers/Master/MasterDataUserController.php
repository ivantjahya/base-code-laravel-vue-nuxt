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
        Log::debug('User is requesting get limit list', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip()]);

        /** Validate Input */
        $validate = Validator::make($request->all(), [
            'username' => ['nullable', 'string'],
            'name' => ['nullable', 'string'],
            'profile' => ['nullable', 'uuid'],
            'category' => ['nullable', 'uuid'],
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
                'category' => $validated['category'] ?? null,
                'search' => $validated['search'] ?? null,
                'skip' => $validated['skip'] ?? null,
                'limit' => $validated['limit'] ?? null,
                'sort_by' => $validated['sort_by'] ?? null,
                'sort_order' => $validated['sort_order'] ?? null,
            ];
            $data = $this->moduleMasterDataService->getLimitList($params);

            return response()->json($data);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }
}
