<?php

namespace App\Http\Controllers;

use App\Interfaces\InterfaceClass;
use App\Models\Master\Status;
use App\Models\User;
use App\Services\PythonModuleMasterDataService;
use App\Traits\AuthFunction;
use App\Traits\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse as HttpJsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    use AuthFunction, JsonResponse;

    private PythonModuleMasterDataService $moduleMasterDataService;

    public function __construct(PythonModuleMasterDataService $moduleMasterDataService)
    {
        $this->moduleMasterDataService = $moduleMasterDataService;
    }

    /**
     * GET request for login landing page
     */
    public function loginPage(Request $request): View
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('Computer access login page', ['userId' => $user?->id, 'userName' => $user?->name, 'route' => $request->route()->getName()]);

        return view('base-components.base-vue', [
            'title' => __('app.page.login'),
        ]);
    }

    /**
     * POST request for login
     */
    public function postLogin(Request $request): HttpJsonResponse
    {
        /** Validate request */
        $validate = Validator::make($request->all(), [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }
        (array) $validated = $validate->validated();

        /** Check username */
        $user = User::where('username', $validated['username'])->first();
        if (is_null($user)) {
            Log::warning('Username failed to login, incorrect username', ['username' => $validated['username']]);
            throw ValidationException::withMessages([
                'username' => __('app.auth.validation.username-incorrect'),
            ]);
        }

        /** Check password */
        if (! Hash::check($validated['password'], $user?->password)) {
            /** Increment login attempt, maximum 5 */
            $user->login_attempt = ($user->login_attempt ?? 0) + 1;
            $user->save();
            if ($user->login_attempt >= 5) {
                /** Lock user if login attempt exceed maximum */
                $statusIdLocked = Cache::tags([InterfaceClass::TAG_MASTERDATA])->remember(InterfaceClass::KEY_STATUS_USER_LOCKED, Carbon::now()->addYear(), function () {
                    return Status::where('code', InterfaceClass::STATUS_USER_LOCKED)->first()?->id;
                });
                $user->status_id = $statusIdLocked;
                $user->save();

                Log::warning('Username failed to login, user locked due to too many failed attempts', ['username' => $validated['username']]);
                throw ValidationException::withMessages([
                    'username' => __('app.auth.validation.locked-user'),
                ]);
            }

            Log::warning('Username failed to login, incorrect password', ['username' => $validated['username']]);
            throw ValidationException::withMessages([
                'username' => __('app.auth.validation.password-incorrect'),
            ]);
        }

        /** Check status user */
        $statusIdInactive = Cache::tags([InterfaceClass::TAG_MASTERDATA])->remember(InterfaceClass::KEY_STATUS_USER_INACTIVE, Carbon::now()->addYear(), function () {
            return Status::where('code', InterfaceClass::STATUS_USER_INACTIVE)->first()?->id;
        });
        $statusIdLocked = Cache::tags([InterfaceClass::TAG_MASTERDATA])->remember(InterfaceClass::KEY_STATUS_USER_LOCKED, Carbon::now()->addYear(), function () {
            return Status::where('code', InterfaceClass::STATUS_USER_LOCKED)->first()?->id;
        });
        if ($user->status_id === $statusIdInactive) {
            Log::warning('Username failed to login, inactive user', ['username' => $validated['username']]);
            throw ValidationException::withMessages([
                'username' => __('app.auth.validation.inactive-user'),
            ]);
        } elseif ($user->status_id === $statusIdLocked) {
            Log::warning('Username failed to login, locked user', ['username' => $validated['username']]);
            throw ValidationException::withMessages([
                'username' => __('app.auth.validation.locked-user'),
            ]);
        } elseif ($user->valid_date < Carbon::now()) {
            Log::warning('Username failed to login, expired user', ['username' => $validated['username']]);
            throw ValidationException::withMessages([
                'username' => __('app.auth.validation.expired-user'),
            ]);
        }

        /** Login with custom auth */
        Auth::login($user);
        $request->session()->regenerate();

        Log::notice('User logged in', ['remoteIp' => $request->ip(), 'username' => $user?->username, 'name' => $user?->name]);

        /** Pre-fetch and cache the access menu list so the frontend can use it immediately */
        /** Keyed by user ID — each user has their own snapshot, refreshed only on login. */
        /** This means profile changes don't affect currently logged-in users until they re-login. */
        $accessMenuList = [];
        try {
            $accessMenuList = Cache::tags([InterfaceClass::TAG_MENUPERM])->remember(InterfaceClass::KEY_MENUPERM.'-'.$user?->profile_id, Carbon::now()->addYear(), function () use ($user) {
                $data = $this->moduleMasterDataService->getProfileMenuAccess($user?->profile_id);

                return $data['data']['menu_access'] ?? [];
            });

            // Always force-write on login so this user gets the latest access
            Cache::tags([InterfaceClass::TAG_MENUPERM])->put(InterfaceClass::KEY_MENUPERM.'-'.$user?->id, $accessMenuList, Carbon::now()->addYear());
        } catch (\Throwable $e) {
            Log::warning('Failed to fetch access menu list on login', ['userId' => $user?->id, 'error' => $e->getMessage()]);
        }

        /** Clear login attempt */
        $user->login_attempt = 0;
        $user->save();

        /** Send user to home */
        (string) $title = __('app.auth.login-success.title');
        (string) $message = __('app.auth.login-success.message');
        (string) $route = route('home');

        return $this->jsonSuccess($title, $message, $route, [
            'accessMenuList' => $accessMenuList,
        ]);
    }

    /**
     * Check if user is authenticated and logout
     */
    private function checkAuthLogout(Request $request): void
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        /** Logout if user is authenticated */
        if (Auth::check()) {
            Log::info('User logging out', ['userId' => $user?->username, 'userName' => $user?->name, 'apiUserIp' => $request->ip()]);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
    }

    /**
     * POST request for logout
     */
    public function postLogout(Request $request): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('Computer Access Logout Request', ['userId' => $user?->username, 'userName' => $user?->name, 'apiUserIp' => $request->ip()]);

        /** Call common logout function */
        $this->checkAuthLogout($request);

        /** Send user to route */
        (string) $title = __('app.auth.logout-success.title');
        (string) $message = __('app.auth.logout-success.message');
        (string) $route = route('login');

        return $this->jsonSuccess($title, $message, $route);
    }

    /**
     * GET request for logout
     */
    public function getLogout(Request $request): RedirectResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('Computer Access Logout Request', ['userId' => $user?->username, 'userName' => $user?->name, 'remoteIp' => $request->ip()]);

        /** Call common logout function */
        $this->checkAuthLogout($request);

        /** Send user to route */
        return redirect()->route('login');
    }

    /**
     * POST request for get API Token
     */
    public function postToken(Request $request): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::debug('Computer access post token', ['userId' => $user?->username, 'userName' => $user?->name, 'apiUserIp' => $request->ip()]);

        /** Validate request */
        $validate = Validator::make($request->all(), [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
        if ($validate->fails()) {
            throw new ValidationException($validate);
        }
        (array) $validated = $validate->validated();

        $validatedLog = $validated;
        unset($validatedLog['password']);
        Log::info('Username getting token validation', ['username' => $validated['username'], 'apiUserIp' => $request->ip(), 'validated' => json_encode($validatedLog)]);

        /** If user not found or password false return failed */
        if (is_null($user = $this->checkAuthUser($validated))) {
            Log::warning('Username failed to login', ['username' => $validated['username'], 'apiUserIp' => $request->ip()]);
            throw ValidationException::withMessages([
                'username' => 'Username or password is incorrect',
            ]);
        }

        Log::info('Username getting token', ['username' => $validated['username'], 'apiUserIp' => $request->ip()]);

        /** Generate user API Token */
        (string) $token = $user->createToken($validated['username'])->accessToken;
        (string) $expire = InterfaceClass::getPassportTokenLifetime()->toDateTimeString();

        Log::notice('Username got token', ['username' => $validated['username'], 'apiUserIp' => $request->ip()]);

        return response()->json([
            'status' => 'success',
            'title' => __('app.token.generated.title'),
            'message' => __('app.token.generated.message'),
            'token_type' => 'Bearer',
            'access_token' => $token,
            'expires_at' => $expire,
        ], 200);
    }

    /**
     * POST request for revoke API Token
     */
    public function postTokenRevoke(Request $request): HttpJsonResponse
    {
        $user = Auth::user() ?? Auth::guard('api')->user();
        Log::info('Username revoking token', ['userId' => $user?->username, 'userName' => $user?->name, 'username' => $user?->name, 'apiUserIp' => $request->ip()]);

        // Cache::forget($user?->username.'-auth-ip-'.$request->ip());

        /** Match bearer token with access token */
        $request->user()->token()->revoke();

        Log::notice('Username revoked token', ['userId' => $user?->username, 'userName' => $user?->name, 'username' => $user?->name, 'apiUserIp' => $request->ip()]);

        return response()->json([
            'status' => 'success',
            'title' => __('app.token.revoked.title'),
            'message' => __('app.token.revoked.message'),
        ], 200);
    }
}
