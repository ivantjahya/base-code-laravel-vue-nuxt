<?php

namespace App\Http\Controllers;

use App\Interfaces\InterfaceClass;
use App\Models\User;
use App\Traits\AuthFunction;
use App\Traits\JsonResponse;
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
            Log::warning('Username failed to login, incorrect password', ['username' => $validated['username']]);
            throw ValidationException::withMessages([
                'username' => __('app.auth.validation.password-incorrect'),
            ]);
        }

        /** Clear old session first */
        // Cache::tags([InterfaceClass::TAG_MENUPERM])->forget(InterfaceClass::KEY_MENUPERM.'-'.$user?->username);
        // Cache::tags([InterfaceClass::TAG_MENUCTRLPERM])->forget(InterfaceClass::KEY_MENUCTRLPERM.'-'.$user?->username);

        /** Login with custom auth */
        Auth::login($user);
        $request->session()->regenerate();

        Log::notice('User logged in', ['remoteIp' => $request->ip(), 'username' => $user?->username, 'name' => $user?->name]);

        /** Send user to dashboard */
        (string) $title = __('app.auth.login-success.title');
        (string) $message = __('app.auth.login-success.message');
        (string) $route = route('dashboard');

        Log::debug('Login response', [
            'title' => $title,
            'message' => $message,
            'locale' => app()->getLocale(),
        ]);

        return $this->jsonSuccess($title, $message, $route);
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

            // Cache::tags([InterfaceClass::TAG_MENUPERM])->forget(InterfaceClass::KEY_MENUPERM.'-'.$user?->username);
            // Cache::tags([InterfaceClass::TAG_MENUCTRLPERM])->forget(InterfaceClass::KEY_MENUCTRLPERM.'-'.$user?->username);
            // Cache::tags([InterfaceClass::TAG_OPENDETAILPAGE.'-'.$user?->username])->flush();
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
        (string) $message = __('app.text.logout-success.message');
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
