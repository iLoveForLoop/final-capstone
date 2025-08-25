<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    // FavoritesController.php
    public function store(Service $service)
    {
        auth()->user()->favorites()->attach($service->id);
        return response()->json(['message' => 'Added to favorites']);
    }

    public function destroy(Service $service)
    {
        auth()->user()->favorites()->detach($service->id);
        return response()->json(['message' => 'Removed from favorites']);
    }

    public function index()
    {
        $favorites = auth()->user()->favorites()
            ->with(['vendor.user', 'category'])
            ->get()
            ->map(fn($service) => [
                'id' => $service->id,
                'vendor_id' => $service->vendor_id,
                'service_category_id' => $service->service_category_id,
                'name' => $service->name,
                'description' => $service->description,
                'price' => $service->price,
                'is_available' => $service->is_available,
                'image_url' => $service->getFirstMediaUrl('images'),
                'category' => $service->category?->name,
                'average_rating' => $service->vendor?->averageRating(),
                'vendor' => [
                    'id' => $service->vendor->id,
                    'business_name' => $service->vendor->business_name,
                    'location' => $service->vendor->location,
                    'profile_image' => $service->vendor->user->getFirstMediaUrl('images'),
                ],
            ]);

        return inertia('Client/Favorites/Index', [
            'favorites' => $favorites
        ]);
    }



}