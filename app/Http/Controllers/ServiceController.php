<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // dd('hi');

        // Ensure the logged-in user has a vendor profile
        if (!$user->vendor) {
            abort(403, 'Unauthorized: Vendor account required.');
        }

        // Base query: vendor's own services
        $query = $user->vendor->services()
            ->with(['category', 'cateringService', 'photographyService']);

        // 🔹 Search filter
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 🔹 Availability filter
        if ($request->availability === 'available') {
            $query->where('is_available', true);
        } elseif ($request->availability === 'unavailable') {
            $query->where('is_available', false);
        }

        // 🔹 Category filter
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('service_category_id', $request->category);
        }

        // 🔹 Pagination + transform for frontend
        $services = $query->paginate(10)->withQueryString()->through(fn($service) => [
            'id'                 => $service->id,
            'vendor_id'          => $service->vendor_id,
            'service_category_id'=> $service->service_category_id,
            'name'               => $service->name,
            'description'        => $service->description,
            'price'              => $service->price,
            'is_available'       => $service->is_available,
            'image_url'          => $service->getFirstMediaUrl('images'),
            'category'           => $service->category,
            'specifications'     => $service->specifications,
            'catering_service'   => $service->cateringService,
            'photography_service'=> $service->photographyService,
            'average_rating'     => $service->vendor->averageRating(),
            'media' => $service->media->map(fn($media) => [
                'id' => $media->id,
                'name' => $media->name,
                'file_name' => $media->file_name,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'original_url' => $media->getUrl(),
                'custom_properties' => $media->custom_properties,
            ]),
        ]);

        // 🔹 Only vendor's categories
        $categories = $user->vendor->serviceCategories()
            ->select('id', 'name')
            ->get();

        // 🔹 Pass filters back
        $filters = $request->only(['search', 'availability', 'category']);

        return inertia('Vendor/Services/Index', compact('services', 'categories', 'filters'));
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

    public function getByCategory($categoryIdentifier, Request $request)
{
    try {
        // Get the event date from request if provided
        $eventDate = $request->query('event_date');

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
            ->map(function($service) use ($eventDate) {
                $isAvailableOnDate = $this->checkServiceAvailabilityOnDate($service, $eventDate);

                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => $service->price,
                    'description' => $service->description,
                    'image_url' => $service->getFirstMediaUrl('images'),
                    'vendor' => $service->vendor ?? null,
                    'rating' => $service->vendor->averageRating(),
                    'dateAdded' => $service->created_at->format('Y-m-d'),
                    'category' => $service->category ? [
                        'id' => $service->category->id,
                        'name' => $service->category->name
                    ] : null,
                    'is_available' => $service->is_available,
                    'is_available_on_date' => $isAvailableOnDate, // New field
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

/**
 * Check if a service is available on a specific date
 */
private function checkServiceAvailabilityOnDate($service, $eventDate)
{
    // If no date provided, return general availability
    if (!$eventDate) {
        return $service->is_available;
    }

    // If service is generally not available, return false
    if (!$service->is_available) {
        return false;
    }

    // Check if the service is already booked on this date with confirmed status
    $hasBooking = \App\Models\Booking::where('service_id', $service->id)
        ->where('booking_date', $eventDate)
        ->whereIn('status', ['confirmed']) // Only confirmed/completed bookings block availability
        ->exists();

    return !$hasBooking;
}

}
