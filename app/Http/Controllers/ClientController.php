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
            'vendor' => $service->vendor,
            'rating' => $service->vendor->averageRating()
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

        // dd('hi');

        return inertia('Client/Favorites/Index');
    }

    public function events(){

        $user = auth()->user();

        $query = $user->events()->with(['bookings.service' => function ($q) {
            $q->with(['category', 'vendor']);
        }]);

        $events = $query->paginate(10)->withQueryString()->through(fn ($event) => [
            'id' => 'EVT' . str_pad($event->id, 3, '0', STR_PAD_LEFT),
            'title' => $event->name,
            'start' => $event->event_date,
            'location' => $event->location,
            'decription' => $event->description,
            'status' => $event->status,
            'services' => $event->bookings->map(fn ($booking) => [
                'name' => $booking->service->category->name,
                'provider' => $booking->service->vendor->business_name,
                'status' => $booking->status
            ]),
            'createdDate' => $event->created_at

        ]);

        return inertia('Client/Events/Index', compact('events'));
    }

}