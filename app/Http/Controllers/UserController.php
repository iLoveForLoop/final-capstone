<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $query = User::with(['vendor', 'roles'])
        ->whereDoesntHave('vendor', function ($query) {
            $query->where('is_approved', false);
        });

    $service_categories = ServiceCategory::all();

    // Apply role filter

    if ($request->has('role') && $request->role !== 'all') {

        $query->role($request->role);
    }


    // Apply search filter
    if ($request->has('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    $users = $query->paginate(5);

    $users->getCollection()->transform(function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'vendor' => $user->vendor,
        'roles' => $user->roles,
        'created_at' => $user->created_at,
        'image_url' =>$user->getFirstMediaUrl('images'),
        'service_categories' => $user->vendor?->serviceCategories ?? [],
        ];
    });

    $filters = $request->only(['role', 'search']);
    // dd($users);

    return inertia(
        'Admin/Users/Index',
        compact('users', 'filters', 'service_categories')
    );
}



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        // dd($request->full_name);
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'profile_image' => 'nullable|image|max:2048',

        ]);

        if($request->selected_role === 'vendor'){
                $request->validate([
                'service_categories' => 'required|array',
                'service_categories.*' => 'exists:service_categories,id',
            ]);
        }





        $user = User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

        if($request->selected_role === 'admin'){
            $user->assignRole('admin');
        }else if($request->selected_role === 'vendor'){
            $vendor = $user->vendor()->create([
                'full_name' => $request->full_name,
                'business_name' => $request->business_name,
                'location' => $request->location,
                'description' => $request->description,
                'is_approved' => true,
                'contact_number' => $request->contact_number
            ]);
            $vendor->serviceCategories()->attach($request->service_categories);

            if($request->profile_image){
                $user->addMediaFromRequest('profile_image')->toMediaCollection('images', 'public');
            }

            $user->assignRole('vendor');
        }else{
            $user->client()->create([
                'full_name' => $user->name,
                'contact_number' => $request->contact_number,
                'location' => $request->location
            ]);
            $user->assignRole('client');
        }

        // $user->addMediaFromRequest('profile_image')->toMediaCollection('images');


        return back()->with('success', 'Vendor added with successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
{

    $user->load(['roles', 'vendor']);
    $userData = [];

    if($user->hasRole('vendor')){
        $user->vendor->load('serviceCategories');
         $userData = [
        'service_categories' => $user->vendor->serviceCategories(),
    ];
    }




    $userData = [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'vendor' => $user->vendor,
        'roles' => $user->roles,
        'created_at' => $user->created_at,
        'image_url' => $user->getFirstMediaUrl('images'),

    ];

    return inertia('Admin/Users/Show', [
        'user' => $userData,
    ]);
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(User $user, Request $request)
{
    $rules = [
        'full_name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
        'password' => 'nullable|confirmed|min:8',
        'profile_image' => 'nullable|image|max:2048',
    ];

    $validated = $request->validate($rules);

    // Update user
    $user->update([
        'name' => $validated['full_name'],
        'email' => $validated['email'],
        'password' => isset($validated['password']) ? Hash::make($validated['password']) : $user->password
    ]);

    // Handle profile image
    if ($request->hasFile('profile_image')) {
        // Clear existing media first if you want to replace
        $user->clearMediaCollection('images');
        $user->addMediaFromRequest('profile_image')->toMediaCollection('images', 'public');
    }

    // Handle vendor/client specific updates
    if ($request->selected_role === 'vendor') {
        $vendorData = [
            'business_name' => $request->business_name,
            'description' => $request->description,
            'contact_number' => $request->contact_number,
            'location' => $request->location,
            'full_name' => $request->full_name
        ];

        $user->vendor()->updateOrCreate([], $vendorData);

        // Sync service categories
        if ($request->has('service_categories')) {
            $user->vendor->serviceCategories()->sync($request->service_categories);
        }
    }

    return redirect()->back()->with('success', 'User updated successfully');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // dd($user);
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }
}
