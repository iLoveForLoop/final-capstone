<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {



        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if($request->is_vendor){
            $user->assignRole('vendor');
            Vendor::create([
                'user_id' => $user->id,
                'business_name' => $request->business_name,
                'location' => $request->location,
                'contact_number' => $request->contact_number
            ]);
        }else{
            $user->assignRole(roles: 'client');
        }

        event(new Registered($user));

        Auth::login($user);

        $redirect = match (true) {
        $user->hasRole('admin') => route('admin.index', absolute: false),
        $user->hasRole('vendor') => route('vendor.index', absolute: false),
        $user->hasRole('client') => route('client.index', absolute: false),
        default => route('dashboard', absolute: false),
    };

    return redirect()->intended($redirect);
    }
}
