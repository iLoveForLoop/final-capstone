<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{

    public function store(Service $service)
    {
        auth()->user()->favorites()->syncWithoutDetaching($service->id);
        return response()->json(['message' => 'Added to favorites']);
    }

    public function destroy(Service $service)
    {

        auth()->user()->favorites()->detach($service->id);
        return response()->json(['message' => 'Removed from favorites']);
    }

    public function index()
    {




        $query = auth()->user()->favorites()->with(['vendor.reviews', 'category']);

        $favorites = $query->paginate(10)->withQueryString()->through(fn ($favorite) => [
            'id' => $favorite->id,
            'name' => $favorite->name,
            'description' => $favorite->description,
            'price' => $favorite->price,
            'image_url' => $favorite->getFirstMediaUrl('images'),
            'category_name' => $favorite->category->name,
            'dateAdded' => $favorite->created_at->format('Y-m-d'),
            'vendor' => $favorite->vendor,
            'rating' => $favorite->vendor->averageRating()
        ]);

            // dd('hi');

        return inertia('Client/Favorites/Index', [
            'favorites' => $favorites
        ]);
    }



}
