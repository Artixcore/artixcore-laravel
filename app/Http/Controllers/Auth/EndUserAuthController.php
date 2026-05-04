<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\RespondsWithWebAuthJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EndUserLoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use App\Services\Auth\PostLoginRedirectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EndUserAuthController extends Controller
{
    use RespondsWithWebAuthJson;

    public function __construct(private ActivityLogger $activityLogger) {}

    public function showLogin(Request $request): View
    {
        return view('auth.end-user-login');
    }

    public function showRegister(Request $request): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request, PostLoginRedirectService $redirectService): RedirectResponse|JsonResponse
    {
        $data = $request->validated();

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'user_kind' => 'external',
            'phone' => $data['phone'] ?? null,
            'company_name' => $data['company_name'] ?? null,
        ]);

        $user->assignRole('end_user');

        Auth::login($user);
        $request->session()->regenerate();

        $this->activityLogger->log('auth.end_user.register', $user, [], $request);

        $target = $redirectService->url($user);

        if ($this->wantsAuthJson($request)) {
            return $this->authJsonSuccess($target);
        }

        return redirect()->intended($target);
    }

    public function login(EndUserLoginRequest $request, PostLoginRedirectService $redirectService): RedirectResponse|JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
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

        if ($user->can('admin.access')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            if ($this->wantsAuthJson($request)) {
                return $this->authJsonValidationError(
                    $request,
                    'Use the admin sign-in page for this account.',
                    ['email' => ['Use the admin sign-in page for this account.']]
                );
            }

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Use the admin sign-in page for this account.']);
        }

        if (! $user->can('portal.access')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            if ($this->wantsAuthJson($request)) {
                return $this->authJsonValidationError(
                    $request,
                    'You are not authorized to use the customer portal.',
                    ['email' => ['You are not authorized to use the customer portal.']]
                );
            }

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'You are not authorized to use the customer portal.']);
        }

        $this->activityLogger->log('auth.end_user.login_success', $user, [], $request);

        $target = $redirectService->url($user);

        if ($this->wantsAuthJson($request)) {
            return $this->authJsonSuccess($target);
        }

        return redirect()->intended($target);
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->activityLogger->log('auth.end_user.logout', $request->user(), [], $request);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

}
