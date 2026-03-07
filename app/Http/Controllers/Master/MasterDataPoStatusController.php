<?php

namespace App\Http\Controllers\Master;

use App\Exceptions\CommonCustomException;
use App\Http\Controllers\Controller;
use App\Services\PythonModuleMasterDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MasterDataPoStatusController extends Controller
{
    private PythonModuleMasterDataService $moduleMasterDataService;

    public function __construct(PythonModuleMasterDataService $moduleMasterDataService)
    {
        $this->moduleMasterDataService = $moduleMasterDataService;
    }

    /**
     * GET request for get po status list
     */
    public function getPoStatusList(Request $request)
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting get po status list', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip()]);

        try {

            $data = $this->moduleMasterDataService->getPoStatusList();

            return response()->json($data);

        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }
}
