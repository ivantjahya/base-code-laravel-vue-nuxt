<?php

namespace App\Http\Controllers;

use App\Interfaces\InterfaceClass;
use App\Interfaces\MenuConst;
use App\Interfaces\MenuCtrlConst;
use Illuminate\Http\JsonResponse as HttpJsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use League\OAuth2\Server\Exception\OAuthServerException;
use ReflectionClass;

class AppConstController extends Controller
{
    /**
     * POST app constants
     */
    public function mainConst(Request $request): HttpJsonResponse
    {

        try {
            $authCheck = Auth::check() ? true : Auth::guard('api')->check();
            $user = Auth::user() ?? Auth::guard('api')->user();
            Log::debug('User is requesting app constants', ['userId' => $user?->id, 'userName' => $user?->name, 'route' => $request->route()->getName()]);
        } catch (OAuthServerException $e) {
            $user = Auth::user() ?? Auth::guard('api')->user();
            Log::warning('Client is requesting app constants but not authenticated', ['userId' => $user?->id, 'userName' => $user?->name, 'route' => $request->route()->getName()]);

            return response()->json([
                'isAuth' => false,
                'accessMenuList' => [],
                'menuCodes' => (new ReflectionClass(MenuConst::class))->getConstants(),
                'ctrlCodes' => (new ReflectionClass(MenuCtrlConst::class))->getConstants(),
            ], 200);
        }

        $accessMenuList = [];
        if ($user) {
            // Keyed by user_id: each user keeps their own menu snapshot set at login.
            // This ensures profile changes only take effect after the user re-logs in.
            $accessMenuList = Cache::tags([InterfaceClass::TAG_MENUPERM])->get(InterfaceClass::KEY_MENUPERM.'-'.$user?->id, []);
        }

        return response()->json([
            'appName' => config('app.name'),
            'appVersion' => '-',
            'appDebug' => config('app.debug'),
            'userName' => $user?->name ?? '',
            'userId' => $user?->id ?? '',
            'profileId' => $user?->profile_id ?? '',
            'isAuth' => $authCheck,
            'accessMenuList' => $accessMenuList,
            'menuCodes' => (new ReflectionClass(MenuConst::class))->getConstants(),
            'ctrlCodes' => (new ReflectionClass(MenuCtrlConst::class))->getConstants(),
        ], 200);
    }
}
