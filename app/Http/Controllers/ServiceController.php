<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // dd('hi');



        // Base query: vendor's own services
        $query = $user->vendor->services()
            ->with(['category', 'cateringService', 'photographyService'])->withCount('bookings');

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
            'bookings_count'     => $service->bookings_count,
            'photography_service'=> $service->photographyService,
            'videography_service'=> $service->videographyService,
            'average_rating'     => $service->vendor->averageRating(),
            'created_at'         => $service->created_at,
            'updated_at'         => $service->updated_at,
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
        $vendor = auth()->user()->vendor;

        // dd($request->service_category_id);

        $validated = $request->validate([
            // General service fields
            'service_category_id' => 'required|integer|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gt:price',

            // Multiple images validation
            'cover_images' => 'nullable|array',
            'cover_images.*' => 'image|mimes:jpeg,png,jpg|max:2048', // Max 2MB per image

            'specifications' => 'nullable|array',
            'specifications.*' => 'string|max:255',


        ]);

        // Additional validation for multiple images
        if ($request->hasFile('cover_images')) {
            $coverImages = $request->file('cover_images');
            if (count($coverImages) > 8) {
                return back()->withErrors(['cover_images' => 'You can upload a maximum of 8 images.'])->withInput();
            }
        }

        DB::beginTransaction();

        try {
            // Create the main service record
            $service = $vendor->services()->create([
                'service_category_id' => $validated['service_category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'specifications' => $validated['specifications'] ?? [],
            ]);



            // Handle multiple cover images upload
            if ($request->hasFile('cover_images')) {
                $coverImages = $request->file('cover_images');

                foreach ($coverImages as $index => $image) {
                    $mediaItem = $service->addMediaFromRequest("cover_images.{$index}")
                        ->usingFileName(uniqid() . '.' . $image->getClientOriginalExtension())
                        ->toMediaCollection('images', 'public');

                    // Mark the first image as primary/cover
                    if ($index === 0) {
                        $mediaItem->setCustomProperty('is_primary', true);
                        $mediaItem->setCustomProperty('is_cover', true);
                        $mediaItem->save();
                    } else {
                        $mediaItem->setCustomProperty('is_portfolio', true);
                        $mediaItem->save();
                    }
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Photography service created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create photography service: ' . $e->getMessage());
        }
    }


    public function toggleAvailability(Service $service){
        $service->update([
            'is_available' => !$service->is_available
        ]);

        return redirect()->back();
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
    public function update(Request $request, $id)
    {
        $vendor = auth()->user()->vendor;
        $service = $vendor->services()->with(['media'])->findOrFail($id);

        $validated = $request->validate([
            'service_category_id' => 'required|integer|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gt:price',

            'specifications' => 'nullable|array',
            'specifications.*' => 'string|max:255',


            // Images validation
            'cover_images' => 'nullable|array',
            'cover_images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer|exists:media,id',
        ]);

        // Additional validation for multiple images
        $currentImageCount = $service->media->count();
        $deletedCount = is_array($request->delete_images) ? count($request->delete_images) : 0;
        $newImageCount = $request->hasFile('cover_images') ? count($request->file('cover_images')) : 0;
        $totalAfterUpdate = $currentImageCount - $deletedCount + $newImageCount;

        if ($totalAfterUpdate > 8) {
            return back()->withErrors(['cover_images' => 'Total images cannot exceed 8. Please remove some existing images first.'])->withInput();
        }

        DB::beginTransaction();

        try {
            // Step 1: Update the general service
            $service->update([
                'service_category_id' => $validated['service_category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'max_price' => $validated['max_price'] ?? null,
                'specifications' => $validated['specifications'] ?? [],
            ]);

            // Step 3: Handle image deletions
            if ($request->has('delete_images') && is_array($request->delete_images)) {
                foreach ($request->delete_images as $mediaId) {
                    $media = $service->media()->find($mediaId);
                    if ($media) {
                        $media->delete();
                    }
                }
            }

            // Step 4: Handle new image uploads
            if ($request->hasFile('cover_images')) {
                $coverImages = $request->file('cover_images');

                foreach ($coverImages as $index => $image) {
                    $mediaItem = $service->addMediaFromRequest("cover_images.{$index}")
                        ->usingFileName(uniqid() . '.' . $image->getClientOriginalExtension())
                        ->toMediaCollection('images', 'public');

                    // Set custom properties for new images
                    $mediaItem->setCustomProperty('is_portfolio', true);
                    $mediaItem->save();
                }
            }

            // Step 5: Ensure we have a primary image
            $allMedia = $service->fresh()->media;
            $hasPrimary = $allMedia->contains(function ($media) {
                return $media->getCustomProperty('is_primary', false);
            });

            if (!$hasPrimary && $allMedia->count() > 0) {
                $firstMedia = $allMedia->first();
                $firstMedia->setCustomProperty('is_primary', true);
                $firstMedia->setCustomProperty('is_cover', true);
                $firstMedia->save();
            }

            DB::commit();

            return back()->with('success', 'Photography service updated successfully');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error updating photography service: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update service. Please try again.'])->withInput();
        }
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
            ->where('is_available', true)
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
                    'specifications' => $service->specifications,
                    'category' => $service->category ? [
                        'id' => $service->category->id,
                        'name' => $service->category->name
                    ] : null,
                    'is_available' => $service->is_available,
                    'is_available_on_date' => $isAvailableOnDate, // New field
                    'catering_service' => $service->cateringService ?? null,
                    'photography_service' => $service->photographyService ?? null,
                    'media' => $service->media->map(fn($media) => [
                        'id' => $media->id,
                        'name' => $media->name,
                        'file_name' => $media->file_name,
                        'mime_type' => $media->mime_type,
                        'size' => $media->size,
                        'original_url' => $media->getUrl(),
                        'custom_properties' => $media->custom_properties,
                    ]),
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
    $confirmedCount = \App\Models\Booking::where('service_id', $service->id)
        ->whereDate('booking_date', $eventDate)
        ->where('status', ['confirmed']) // Only confirmed/completed bookings block availability
        ->count();

    return $confirmedCount < 1;
}

}
