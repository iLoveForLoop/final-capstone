<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response|\Illuminate\Http\RedirectResponse
    {
        if (auth()->check()) {
            return RedirectHelper::redirectBasedOnRole(auth()->user());
        }

        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }


    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();

    // $redirect = match (true) {
    //     $user->hasRole('admin') => route('admin.index', absolute: false),
    //     $user->hasRole('vendor') => route('vendor.index', absolute: false),
    //     $user->hasRole('client') => route('client.index', absolute: false),
    //     default => route('dashboard', absolute: false),
    // };

    // dd($redirect);

    // return redirect()->intended($redirect);
    return RedirectHelper::redirectBasedOnRole($user);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
