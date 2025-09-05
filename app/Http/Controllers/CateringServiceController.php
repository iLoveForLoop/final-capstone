<?php

namespace App\Http\Controllers;

use App\Models\CateringService;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CateringServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    $vendor = auth()->user()->vendor;
    $vendor->load('serviceCategories');

    if(!$request->price){
        $request->validate([
            'package_price' => 'required|numeric'
        ]);
    }

    if(!$request->package_price){
        $request->validate([
            'price' => 'required|numeric'
        ]);
    }

    if(!$request->package_price && !$request->price){
        $request->validate([
            'price' => 'required|numeric',
            'package_price' => 'required|numeric'
        ]);
    }

    $validated = $request->validate([
        'service_category_id' => 'required|integer|exists:service_categories,id',
        'name' => 'required|string',
        'description' => 'nullable|string',
        // 'price' => 'required|numeric',
        'max_price' => 'nullable|numeric',

        // Catering specific
        'min_pax' => 'required|integer',
        'max_pax' => 'required|integer',
        // 'package_price' => 'nullable|numeric',
        'lead_time_days' => 'nullable|integer',
        'service_area' => 'nullable|array',
        'is_customizable' => 'nullable|boolean',
        'delivery_fee' => 'nullable|numeric',
        'buffet_type' => 'nullable|string',
        'specifications' => 'nullable|array',
        'dishes' => 'required|array',
        'dish_selection_limits' => 'nullable|array',
        'notes' => 'nullable|string',

        // Multiple images validation
        'cover_images' => 'nullable|array',
        'cover_images.*' => 'image|mimes:jpeg,png,jpg|max:2048', // Max 2MB per image
    ]);

    // Additional validation for multiple images
    if ($request->hasFile('cover_images')) {
        $coverImages = $request->file('cover_images');
        if (count($coverImages) > 5) {
            return back()->withErrors(['cover_images' => 'You can upload a maximum of 5 images.'])->withInput();
        }
    }

    // DB::beginTransaction();

    // Step 1: Create the general service
    $service = $vendor->services()->create([
        'service_category_id' => $validated['service_category_id'],
        'name' => $validated['name'],
        'description' => $validated['description'] ?? null,
        'price' => $request->price ?? $request->package_price,
        'max_price' => $validated['max_price'] ?? null,
        'specifications' => $validated['specifications'] ?? [],
    ]);

    // Step 2: Create the related catering service
    $service->cateringService()->create([
        'name' => $validated['name'], // or a separate catering name
        'min_pax' => $validated['min_pax'],
        'max_pax' => $validated['max_pax'],
        'price' => $request->price ?? $request->package_price,
        'package_price' => $request->package_price ?? null,
        'lead_time_days' => $validated['lead_time_days'] ?? 3,
        'service_area' => $validated['service_area'] ?? [],
        'is_customizable' => $validated['is_customizable'] ?? false,
        'delivery_fee' => $validated['delivery_fee'] ?? null,
        'buffet_type' => $validated['buffet_type'] ?? null,
        'specifications' => $validated['specifications'] ?? [],
        'dishes' => $validated['dishes'] ?? [],
        'dish_selection_limits' => $validated['dish_selection_limits'] ?? [],
        'notes' => $validated['notes'] ?? null,
    ]);

    // Step 3: Handle multiple cover images
    if ($request->hasFile('cover_images')) {
        $coverImages = $request->file('cover_images');

        foreach ($coverImages as $index => $image) {
            $mediaItem = $service->addMediaFromRequest("cover_images.{$index}")
                ->usingFileName(uniqid() . '.' . $image->getClientOriginalExtension())
                ->toMediaCollection('images', 'public');

            // Mark the first image as primary
            if ($index === 0) {
                $mediaItem->setCustomProperty('is_primary', true);
                $mediaItem->save();
            }
        }
    }

    return back()->with('success', 'Service added successfully');
}

    /**
     * Display the specified resource.
     */
    public function show(CateringService $cateringService)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CateringService $cateringService)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CateringService $cateringService)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CateringService $cateringService)
    {
        //
    }
}