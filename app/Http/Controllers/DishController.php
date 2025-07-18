<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use Illuminate\Http\Request;

class DishController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $vendor = auth()->user()->vendor;

        if (!$vendor) {
            abort(403, 'You are not a registered vendor.');
        }

        $dishes = $vendor->dishes()
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->availability === 'available', function($query) {
                $query->where('is_available', true);
            })
            ->when($request->availability === 'unavailable', function($query) {
                $query->where('is_available', false);
            })
            ->paginate(10);

        $dishes->getCollection()->transform(function ($dish) {
            return [
                'id' => $dish->id,
                'name' => $dish->name,
                'description' => $dish->description,
                'dietary_type' => $dish->dietary_type,
                'course_type' => $dish->course_type,
                'is_available' => $dish->is_available,
                'image_url' => $dish->getFirstMediaUrl('dishes'),
                // Include price if you're using it in your frontend
                // 'price' => $dish->price,
                // Include categories if you're using them in your frontend
                // 'categories' => $dish->categories->map(function($category) {
                //     return [
                //         'id' => $category->id,
                //         'name' => $category->name
                //     ];
                // })
            ];
        });

        return inertia('Vendor/Dishes/Index', [
            'dishes' => $dishes,
            'filters' => $request->only(['search', 'availability'])
        ]);
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

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dish_image' => 'nullable|image|max:2048',
            'dietary_type' => 'nullable|string',
            'course_type' => 'nullable|string',
            'is_available' => 'boolean'

        ]);

        $dish = Dish::create([
            'name' => $request->name,
            'vendor_id' => $vendor->id,
            'description' => $request->description,
            'dietary_type' => $request->dietary_type,
            'course_type' => $request->course_type,
            'is_available' => $request->is_available
        ]);

        if($request->dish_image){
            $dish->addMediaFromRequest('dish_image')->toMediaCollection('dishes', 'public');
        }


        return redirect()->back()->with('success', 'Dish Added Successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(Dish $dish)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dish $dish)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dish $dish)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dish_image' => 'nullable|image|max:2048',
            'dietary_type' => 'nullable|string',
            'course_type' => 'nullable|string',
            'is_available' => 'boolean'

        ]);

        if($request->dish_image){
            $dish->clearMediaCollection('dishes');
            $dish->addMediaFromRequest('dish_image')->toMediaCollection('dishes', 'public');
        }

        $dish->update([
            'name' => $request->name,
            'description' => $request->description,
            'dietary_type' => $request->dietary_type,
            'course_type' => $request->course_type,
            'is_available' => $request->is_available
        ]);

        return back()->with('success', 'Dish Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dish $dish)
    {
        $dish->delete();


        return back()->with('success', 'Deleted Successfully');


    }

    public function toggleAvailability(Dish $dish){
        $dish->update([
            'is_available' => !$dish->is_available
        ]);

        return back();
    }
}