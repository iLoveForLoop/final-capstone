<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
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
            // dd('here');

            Vendor::create([
                'user_id' => $user->id,
                'full_name' => $user->name,
                'business_name' => $request->business_name,
                'location' => $request->location,
                'is_approved' => false,
                'contact_number' => $request->contact_number
            ]);
            $user->vendor->serviceCategories()->attach(1);

            return redirect()->route('login')->with('info', 'Registered Successfully. Please wait for admin approval.');

        }else{
            $user->assignRole('client');
            Client::create([
                'user_id' => $user->id,
                'full_name' => $user->name,
                'contact_number' => $request->contact_number,
                'location' => $request->location
            ]);
        }


        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('client.index');
    }
}