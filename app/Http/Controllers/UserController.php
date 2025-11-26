<?php

namespace App\Http\Controllers;

use App\Mail\Admin\UserBanMail;
use App\Mail\Admin\UserSuspendedMail;
use App\Models\ServiceCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{

    // dd('hi');

    $query = User::with(['vendor', 'roles', 'client'])
        ->whereDoesntHave('vendor', function ($query) {
            $query->where('is_approved', false);
        });

    $service_categories = ServiceCategory::all();

    // Apply role filter
    if ($request->has('role') && $request->role !== 'all') {
        $query->role($request->role);
    }

    // Apply status filter
    if ($request->has('status') && $request->status !== 'all') {
        $query->where('status', $request->status);
    }

    // Apply search filter
    if ($request->has('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    $users = $query->paginate(10);
    // dd('here');
    $users->getCollection()->transform(function ($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'vendor' => $user->vendor ?? null,
            'client' => $user->client ?? null,
            'roles' => $user->roles,
            'created_at' => $user->created_at,
            'image_url' => $user->getFirstMediaUrl('avatar'),
            'service_categories' => $user->vendor?->serviceCategories ?? [],
            'status' => $user->status,
            'activities' => $user->activities->map(function ($activity) {
            return [
                'description' => $activity->description,
                'causer_id' => $activity->causer_id,
                'properties' => $activity->properties,
                'created_at' => $activity->created_at,
                    ];
                }),
        ];
    });

    $filters = $request->only(['role', 'search', 'status']);

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
                $user->addMediaFromRequest('profile_image')->toMediaCollection('avatar', 'public');
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


        return back()->with('success', 'Vendor added with successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
{

    $user->load(['roles', 'vendor', 'client']);
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
        'vendor' => $user->vendor ?? null,
        'client' => $user->client ?? null,
        'roles' => $user->roles,
        'created_at' => $user->created_at,
        'image_url' => $user->getFirstMediaUrl('avatar'),
        'status' => $user->status,
        'activities' => $user->activities->sortByDesc('created_at')->values()->map(function ($activity) {
            return [
                'description' => $activity->description,
                'causer_id' => $activity->causer_id,
                'properties' => $activity->properties,
                'created_at' => $activity->created_at,
                    ];
                }),


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

    //      dd([
    //     'request_data' => $request->all(),
    //     'files' => $request->files->all(),
    //     'has_profile_image' => $request->hasFile('profile_image'),
    //     'remove_profile_image' => $request->boolean('remove_profile_image')
    // ]);

        $rules = [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|confirmed|min:8',
            'profile_image' => 'nullable|image|max:2048',
            'contact_number' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
        ];

        $validated = $request->validate($rules);




        // Update user
        $userData = [
            'name' => $validated['full_name'],
            'email' => $validated['email'],
            // 'password' => isset($validated['password']) ? Hash::make($validated['password']) : $user->password
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        // Handle profile image
        if ($request->hasFile('profile_image')) {
            // Clear existing media first if you want to replace
            $user->clearMediaCollection('avatar');
            $user->addMediaFromRequest('profile_image')->toMediaCollection('avatar', 'public');
        }

        // Handle avatar removal
        if ($request->boolean('remove_profile_image')) {
            $user->clearMediaCollection('avatar');
        }

        // Handle vendor/client specific updates
        if ($user->vendor) {
            $vendorData = [
                'business_name' => $request->business_name,
                'description' => $request->description,
                'contact_number' => $request->contact_number,
                'location' => $request->location,
                'full_name' => $request->full_name
            ];

            $user->vendor()->updateOrCreate([], $vendorData);


            if ($request->has('service_categories')) {
                $user->vendor->serviceCategories()->sync($request->service_categories);
            }
        }

        if ($user->client) {

            if($request->first_name && $request->last_name){
                $clientData = [
                    'contact_number' => $request->contact_number,
                    'location' => $request->location,
                    'full_name' => $request->first_name . ' ' . $request->last_name,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                ];
            }else{
                $clientData = [
                    'contact_number' => $request->contact_number,
                    'location' => $request->location,
                    'full_name' => $request->full_name,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                ];
            }



            // dd($clientData['full_name']);

            $user->client()->updateOrCreate([], $clientData);

        }

        // Log the activation
                        activity()
                            ->causedBy(auth()->user())
                            ->performedOn($user)
                            ->withProperties([
                                'action' => 'activate',
                                // 'previous_status' => $previousStatus,
                                'reason' => $request->reason,
                                'ip_address' => $request->ip(),
                            ])
                            ->log('Admin Updated My Profile');

                            // dd('here');




        // dd('Client Num: '. $user->client->contact_number);
        return redirect()->back()->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        try {
            DB::transaction(function () use ($user) {
                // Log the deletion action before deleting
                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($user)
                    ->withProperties([
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                        'user_name' => $user->name,
                    ])
                    ->log('User permanently deleted');

                // Delete the user
                $user->delete();
            });

            // return response()->json([
            //     'success' => true,
            //     'message' => 'User deleted successfully.',
            // ]);

            return redirect()->back()->with('success', 'User deleted successfully.');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    //status update
    public function updateStatus(Request $request, User $user)
    {



        $request->validate([
            'action' => 'required|in:activate,suspend,ban',
            'reason' => 'nullable|string|max:500',
            'suspended_until' => 'nullable|date|after:now',
        ]);



        try {
            DB::transaction(function () use ($request, $user) {
                $previousStatus = $user->status;

                switch ($request->action) {
                    case 'activate':
                        $user->update([
                            'status' => 'active',
                            'suspended_until' => null,
                            'suspension_reason' => null,
                            'ban_reason' => null,
                        ]);

                        // Log the activation
                        activity()
                            ->causedBy(auth()->user())
                            ->performedOn($user)
                            ->withProperties([
                                'action' => 'activate',
                                'previous_status' => $previousStatus,
                                'reason' => $request->reason,
                                'ip_address' => $request->ip(),
                            ])
                            ->log('user_activated');
                        break;

                    case 'suspend':

                        $user->update([
                            'status' => 'suspended',
                            'suspended_until' => $request->suspended_until,
                            'suspension_reason' => $request->reason,
                            'ban_reason' => null,
                        ]);

                        // 📨 Send Suspension Email
                        $suspensionType = $request->suspended_until ? 'temporary' : 'indefinite';
                        $role = $user->role ?? 'user'; // or whatever field holds the user’s role

                        // dd('here');

                        Mail::to($user->email)->queue(new UserSuspendedMail($user, $suspensionType, $role));


                        // Log the suspension
                        activity()
                            ->causedBy(auth()->user())
                            ->performedOn($user)
                            ->withProperties([
                                'action' => 'suspend',
                                'previous_status' => $previousStatus,
                                'reason' => $request->reason,
                                'suspended_until' => $request->suspended_until,
                                'ip_address' => $request->ip(),
                            ])
                            ->log('user_suspended');
                        break;

                    case 'ban':
                        $user->update([
                            'status' => 'banned',
                            'suspended_until' => null,
                            'suspension_reason' => null,
                            'ban_reason' => $request->reason,
                        ]);

                        $user->load('roles');
                        $role = $user->getRoleNames()->first(); // e.g. "vendor"

                        // dd($user->email);

                        Mail::to($user->email)->queue(new UserBanMail($user, $role));
                        // Log the ban
                        activity()
                            ->causedBy(auth()->user())
                            ->performedOn($user)
                            ->withProperties([
                                'action' => 'ban',
                                'previous_status' => $previousStatus,
                                'reason' => $request->reason,
                                'ip_address' => $request->ip(),
                            ])
                            ->log('user_banned');
                        break;
                }
            });

            // return response()->json([
            //     'success' => true,
            //     'message' => "User {$request->action}ed successfully.",
            // ]);

            return redirect()->back()->with('success', "User {$request->action}ed successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getActivityLog(User $user)
    {
        $activities = $user->activities()
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'description' => $activity->description,
                    'properties' => $activity->properties,
                    'causer' => $activity->causer ? [
                        'id' => $activity->causer->id,
                        'name' => $activity->causer->name,
                        'email' => $activity->causer->email,
                    ] : null,
                    'created_at' => $activity->created_at->toDateTimeString(),
                    'time_ago' => $activity->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'activities' => $activities,
        ]);
    }
}
