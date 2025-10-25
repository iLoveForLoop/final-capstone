<?php

namespace App\Http\Controllers;

use App\Models\VideographyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VideographyServiceController extends Controller
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

            'studio_shoot_available' => 'boolean',
            'specifications' => 'nullable|array',
            'specifications.*' => 'string|max:255',

            // Notes
            'notes' => 'nullable|string'
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

            // Create the photography service record
            $service->videographyService()->create([
                'specifications' => $validated['specifications'] ?? [],
                'notes' => $validated['notes'] ?? null,
                'studio_shoot_available' => $validated['studio_shoot_available'] ?? false,
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
                ->with('success', 'Videography service created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create Videography service: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(VideographyService $videographyService)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VideographyService $videographyService)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
        public function update(Request $request, $id)
{
    $vendor = auth()->user()->vendor;
    $service = $vendor->services()->with(['videographyService', 'media'])->findOrFail($id);

    $validated = $request->validate([
        'service_category_id' => 'required|integer|exists:service_categories,id',
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'max_price' => 'nullable|numeric|min:0|gt:price',

        // Photography specific
        'studio_shoot_available' => 'boolean',
        'specifications' => 'nullable|array',
        'specifications.*' => 'string|max:255',
        'notes' => 'nullable|string',

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

        // Step 2: Update the related photography service
        $service->videographyService->update([
            'specifications' => $validated['specifications'] ?? [],
            'notes' => $validated['notes'] ?? null,
            'studio_shoot_available' => $validated['studio_shoot_available'] ?? false,
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

        return back()->with('success', 'Videography service updated successfully');

    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('Error updating videography service: ' . $e->getMessage());
        return back()->withErrors(['error' => 'Failed to update service. Please try again.'])->withInput();
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VideographyService $videographyService)
    {
        //
    }
}
