<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ServiceCategory;
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
        $categories = ServiceCategory::all();
        return Inertia::render('Auth/Register', compact('categories'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {





        try {
            $request->validate([
            // User
            'full_name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

            // Vendor (only if is_vendor is true)
            'businessName'      => ['required_if:is_vendor,true', 'string', 'max:255'],
            'businessDescription' => ['nullable', 'string'],
            'address'           => ['required', 'string', 'max:255'],
            'phoneNumber'     => ['required', 'string', 'max:20'],

            // Vendor extras
            'vendorCategories'   => ['required_if:is_vendor,true', 'array', 'min:1'],
            'vendorCategories.*' => ['integer'],
            'latitude'           => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'          => ['nullable', 'numeric', 'between:-180,180'],
            'serviceCoverageAreas'   => ['nullable', 'array'],
            'serviceCoverageAreas.*' => ['string', 'max:255'],

            // Files
            'profilePhoto'       => ['nullable', 'image', 'max:2048'], // goes to User
            'servicePhotos'      => ['nullable', 'array', ],
            'servicePhotos.*'    => ['image', 'max:4096'],
            'permitFiles'        => ['nullable', 'array'],
            'permitFiles.*'      => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);
            // dd('here');
        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->errors());
        }



        // Create the user
        $user = User::create([
            'name'     => $request->full_name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Handle vendor registration
        if ($request->boolean('is_vendor')) {
            $user->assignRole('vendor');

            $vendor = $user->vendor()->create([
                'full_name'              => $request->full_name,
                'business_name'          => $request->businessName,
                'description'            => $request->businessDescription,
                'location'               => $request->address,
                'is_approved'            => false,
                'contact_number'         => $request->phoneNumber,
                'latitude'               => $request->latitude,
                'longitude'              => $request->longitude,
                'service_coverage_areas' => $request->serviceCoverageAreas,
        ]);

            // Attach service categories (pivot, if needed)
            if ($request->filled('vendorCategories')) {
                $vendor->serviceCategories()->sync($request->vendorCategories);
            }

            // Attach media with Spatie
            if ($request->hasFile('profilePhoto')) {
                $user->addMedia($request->file('profilePhoto'))
                    ->toMediaCollection('avatar');
            }

            foreach ($request->file('servicePhotos', []) as $photo) {
                $vendor->addMedia($photo)->toMediaCollection('portfolioImages');
            }

            foreach ($request->file('permitFiles', []) as $permit) {
                $vendor->addMedia($permit)->toMediaCollection('permits');
            }

            return redirect()
                ->route('login')
                ->with('info', 'Registered Successfully. Please wait for admin approval.');
        }

        // Handle client registration
        $user->assignRole('client');
        $user->client()->create([
            'full_name'      => $request->name,
            'contact_number' => $request->phoneNumber,
            'location'       => $request->address,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('client.index');
    }
}
