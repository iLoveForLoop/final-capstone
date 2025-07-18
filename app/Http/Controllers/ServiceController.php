<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
        {
            $vendor = auth()->user()->vendor;

            $services = $vendor->services()->paginate(10);

            $services->getCollection()->transform(function ($service) {
                return [
                    'id' => $service->id,
                    'vendor_id' => $service->vendor_id,
                    'service_category_id' => $service->service_category_id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'price' => $service->price,
                    'is_available' => $service->is_available,
                    'image_url' =>$service->getFirstMediaUrl('images')
                ];
            });

            if ($vendor->serviceCategories()->where('name', 'catering')->exists()) {
                return inertia('Vendor/Services/Catering/Index', compact('services'));
            }

            return inertia('Vendor/Services/Index', compact('services'));
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
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
    ]);



    $vendor = auth()->user()->vendor;





    if (!$vendor) {
        dd('error');
        return redirect()->back()->withErrors(['error' => 'You are not registered as a vendor.']);
    }



    $vendor->services()->create([
        'service_category_id' => 1,
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
    ]);


    return redirect()->back()->with('success', 'Service added successfully.');
}


    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->back()->with('success', 'Service Deleted Successfully');
    }
}
