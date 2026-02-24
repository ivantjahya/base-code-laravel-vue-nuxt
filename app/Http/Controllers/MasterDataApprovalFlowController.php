<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MasterDataApprovalFlowController extends Controller
{
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
}
