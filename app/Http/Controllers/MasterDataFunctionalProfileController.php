<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MasterDataFunctionalProfileController extends Controller
{

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

}
