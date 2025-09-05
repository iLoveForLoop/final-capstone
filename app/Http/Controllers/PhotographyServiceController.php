<?php

namespace App\Http\Controllers;

use App\Models\PhotographyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhotographyServiceController extends Controller
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
        $service->photographyService()->create([
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
            ->with('success', 'Photography service created successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()
            ->with('error', 'Failed to create photography service: ' . $e->getMessage());
    }
}

    /**
     * Display the specified resource.
     */
    public function show(PhotographyService $photographyService)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PhotographyService $photographyService)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PhotographyService $photographyService)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PhotographyService $photographyService)
    {
        //
    }
}
