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

        // dd($request->service_category_id);


        $validated = $request->validate([
            // General service fields
            'service_category_id' => 'required|integer|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gt:price',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            // Photography specific fields
            // 'coverage_type' => 'nullable',
            // 'hours_of_coverage' => 'nullable|integer',
            // 'delivery_time_days' => 'nullable|integer',
            // 'number_of_photographers' => 'nullable|integer',
            'studio_shoot_available' => 'boolean',

            // Deliverables and specifications
            // 'deliverables' => 'nullable|array|',
            // 'deliverables.*' => 'string|max:255',
            'specifications' => 'nullable|array',
            'specifications.*' => 'string|max:255',

            // Notes
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            // Create the main service record
            $service = $vendor->services()->create([
                'service_category_id' => $validated['service_category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                // 'max_price' => $validated['max_price'] ?? null,
            ]);

            // Create the photography service record
            $service->photographyService()->create([
                // 'coverage_type' => $validated['coverage_type'],
                // 'hours_of_coverage' => $validated['hours_of_coverage'] ?? null,
                // 'delivery_time_days' => $validated['delivery_time_days'] ?? 30,
                // 'number_of_photographers' => $validated['number_of_photographers'] ?? 1,
                // 'deliverables' => $validated['deliverables'],
                'specifications' => $validated['specifications'] ?? [],
                'notes' => $validated['notes'] ?? null,
                'studio_shoot_available' => $validated['studio_shoot_available'] ?? false,
            ]);

            // Handle cover image upload
            if ($request->hasFile('cover_image')) {
                $service->addMediaFromRequest('cover_image')
                    ->toMediaCollection('images', 'public');
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