<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse as HttpJsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use League\OAuth2\Server\Exception\OAuthServerException;

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
            ], 200);
        }

        return response()->json([
            'appName' => config('app.name'),
            'appVersion' => '-',
            'appDebug' => config('app.debug'),
            'userName' => $user?->name ?? '',
            'userId' => $user?->id ?? '',
            'isAuth' => $authCheck,
        ], 200);
    }
}
