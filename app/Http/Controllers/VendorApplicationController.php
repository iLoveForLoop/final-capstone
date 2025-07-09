<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $pending_applications = User::where('is_approved', false)->get();
         $pending_applications = Vendor::with('user')->where('is_approved', false)->get();

        return inertia('Admin/PendingApplication/Index', compact('pending_applications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function approve(Vendor $vendor)
    {
        $vendor->update(['is_approved' => true]);
        return back()->with('success', 'Vendor approved successfully');
    }

    public function reject(Vendor $vendor)
    {
        $vendor->user->delete();
        return back()->with('success', 'Vendor application rejected');
    }
}
