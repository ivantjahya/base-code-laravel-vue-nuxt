<?php

namespace App\Http\Controllers\Master;

use App\Exceptions\CommonCustomException;
use App\Http\Controllers\Controller;
use App\Interfaces\InterfaceClass;
use App\Services\PythonModuleMasterDataService;
use Illuminate\Http\JsonResponse as HttpJsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MasterDataCompanyController extends Controller
{
    private PythonModuleMasterDataService $moduleMasterDataService;

    public function __construct(PythonModuleMasterDataService $moduleMasterDataService)
    {
        $this->moduleMasterDataService = $moduleMasterDataService;
    }

    /**
     * GET request for get company list
     */
    public function getCompanyList(Request $request): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting get company list', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip()]);

        try {
            $data = Cache::tags([InterfaceClass::TAG_MASTERDATA])->remember(InterfaceClass::KEY_MASTER_COMPANY_LIST, InterfaceClass::CACHE_MST_TIME, function () {
                $temp = $this->moduleMasterDataService->getCompanyList();

                return $temp;
            });

            return response()->json($data);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }
}
