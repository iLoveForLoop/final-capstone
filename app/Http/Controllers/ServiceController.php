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


            $categories = $vendor->serviceCategories;

            // dd($categories);


            $service_categories = $vendor->serviceCategories()->select('id', 'name')->get();

            $services->getCollection()->transform(function ($service) {
                    return [
                        'id' => $service->id,
                        'vendor_id' => $service->vendor_id,
                        'service_category_id' => $service->service_category_id,
                        'name' => $service->name,
                        'description' => $service->description,
                        'price' => $service->price,
                        'is_available' => $service->is_available,
                        'image_url' =>$service->getFirstMediaUrl('images'),
                        'category' => $service->category,
                        'catering_service' => $service->cateringService ?? null,
                        'photography_service' => $service->photographyService ?? null
                    ];
                });

            // if ($vendor->serviceCategories()->where('name', 'catering')->exists()) {


            //     $dishes = $vendor->dishes()->paginate(10);

            //     return inertia('Vendor/Services/Catering/Index', compact('services', 'dishes', 'service_categories'));
            // }



            return inertia('Vendor/Services/Index', compact('services', 'categories', 'service_categories'));
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

    public function getByCategory($categoryIdentifier)
    {
        try {
            $services = Service::with(['vendor', 'category'])
                ->whereHas('category', function($query) use ($categoryIdentifier) {
                    // Match by ID if numeric, otherwise by slug/name
                    if (is_numeric($categoryIdentifier)) {
                        $query->where('id', $categoryIdentifier);
                    } else {
                        $query->where('slug', $categoryIdentifier)
                            ->orWhere('name', $categoryIdentifier);
                    }
                })
                ->get()
                ->map(function($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'price' => $service->price,
                        'image_url' =>$service->getFirstMediaUrl('images'),
                        'vendor' => $service->vendor ? [
                            'id' => $service->vendor->id,
                            'name' => $service->vendor->full_name
                        ] : null,
                        'category' => $service->category ? [
                            'id' => $service->category->id,
                            'name' => $service->category->name
                        ] : null,
                        'is_available' => $service->is_available,
                        'catering_service' => $service->cateringService ?? null,
                        'photography_service' => $service->photographyService ?? null
                    ];
                });



            return response()->json([
                'success' => true,
                'data' => $services
            ]);

        } catch (\Exception $e) {
            \Log::error("Service fetch failed: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => 'Could not load services',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

}