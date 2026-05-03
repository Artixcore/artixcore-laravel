<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\RespondsWithWebAuthJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Services\Audit\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    use RespondsWithWebAuthJson;

    public function __construct(private ActivityLogger $activityLogger) {}

    public function showLogin(Request $request): View
    {
        return view('auth.admin-login');
    }

    public function login(AdminLoginRequest $request): RedirectResponse|JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            $this->activityLogger->log('auth.admin.login_failed', null, [
                'email_hash' => hash('sha256', strtolower($credentials['email'] ?? '')),
            ], $request);

            if ($this->wantsAuthJson($request)) {
                return $this->authJsonGenericFailure($request);
            }

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $request->session()->regenerate();
        $user = $request->user();
        if ($user === null) {
            Auth::logout();

            return back()->withErrors(['email' => 'These credentials do not match our records.']);
        }

        if (! $user->can('admin.access')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $this->activityLogger->log('auth.admin.login_denied_role', null, [
                'email_hash' => hash('sha256', strtolower((string) $user->email)),
            ], $request);

            if ($this->wantsAuthJson($request)) {
                return $this->authJsonValidationError(
                    $request,
                    'These credentials do not match our records.',
                    ['email' => ['These credentials do not match our records.']]
                );
            }

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $this->activityLogger->log('auth.admin.login_success', $user, [], $request);

        $target = $user->hasRole('master_admin')
            ? route('master.dashboard')
            : route('admin.dashboard');

        if ($this->wantsAuthJson($request)) {
            return $this->authJsonSuccess($target);
        }

        return redirect()->intended($target);
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->activityLogger->log('auth.admin.logout', $request->user(), [], $request);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

}
