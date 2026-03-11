<?php

namespace App\Http\Controllers\Master;

use App\Exceptions\CommonCustomException;
use App\Http\Controllers\Controller;
use App\Interfaces\InterfaceClass;
use App\Services\PythonModuleMasterDataService;
use Illuminate\Http\JsonResponse as HttpJsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MasterDataMerchStructController extends Controller
{
    private PythonModuleMasterDataService $moduleMasterDataService;

    public function __construct(PythonModuleMasterDataService $moduleMasterDataService)
    {
        $this->moduleMasterDataService = $moduleMasterDataService;
    }

    /**
     * GET request for get merch struct div cat list
     */
    public function getMerchStructDivCatList(Request $request): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('User is requesting get merch struct div cat list', ['userId' => $user?->id, 'userName' => $user?->name, 'apiUserIp' => $request->ip()]);

        try {
            if (App::environment('production') || App::environment('staging')) {
                $cacheKey = InterfaceClass::KEY_MASTER_MERCH_STRUCT_DIV_CAT;
            } else {
                $cacheKey = InterfaceClass::KEY_MASTER_MERCH_STRUCT_DIV_CAT.'_'.$user?->id;
            }

            $data = Cache::tags([InterfaceClass::TAG_MASTERDATA])->remember($cacheKey, InterfaceClass::CACHE_MST_TIME, function () {
                $temp = $this->moduleMasterDataService->getMerchStructDivCatList();

                return $temp;
            });

            return response()->json($data);
        } catch (\Throwable $e) {
            throw new CommonCustomException($e->getMessage(), 500, $e);
        }
    }
}
