<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $query = Service::with(['category', 'vendor']);
        $categories = ServiceCategory::all();

        $services = $query->paginate(8)->withQueryString()->through(fn($service) => [

            'id' => $service->id,
            'name' => $service->name,
            'description' => $service->description,
            'price' => $service->price,
            'image_url' => $service->getFirstMediaUrl('images'),
            'category_name' => $service->category->name,
            'dateAdded' => $service->created_at->format('Y-m-d'),
            'vendor' => $service->vendor
        ]);


    return inertia('Client/Index', compact('services', 'categories'));

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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        //
    }

    public function favorites(){

        return inertia('Client/Favorites/Index');
    }

    public function events(){

        $events = auth()->user()->events()->get();
        // dd($events);
        return inertia('Client/Events/Index', compact('events'));
    }
}