<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Vendor;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {


        $query = Service::with(['category', 'vendor', 'cateringService']);
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
            'rating' => $service->vendor->averageRating(),
            'is_available' => $service->is_available,
            'catering_service' => $service->cateringService ?? null
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

    public function services(Request $request){

        // dd('hi');

        $user = auth()->user();

        $categories = ServiceCategory::all();
        $query = Service::with(['category', 'vendor.reviews.user.client', 'cateringService', 'photographyService', 'vendor.user']);


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
        if ($request->category && $request->category !== 'all') {
            $query->where('service_category_id', $request->category);
        }

        // 🔹 Pagination + transform for frontend
        $services = $query->paginate(10)->withQueryString()->through(fn($service) => [
            'id' => $service->id,
            'name' => $service->name,
            'description' => $service->description,
            'price' => $service->price,
            'image_url' => $service->getFirstMediaUrl('images'),
            'category_name' => $service->category->name,
            'dateAdded' => $service->created_at->format('Y-m-d'),
            'vendor' => $service->vendor,
            'rating' => $service->vendor->averageRating(),
            'is_available' => $service->is_available,
            'catering_service' => $service->cateringService ?? null
        ]);



        // 🔹 Pass filters back to Inertia
        $filters = $request->only(['search', 'availability', 'category']);




        return inertia('Client/Services/Index', compact('filters', 'services', 'categories'));


    }

    private function specsChecker(Service $service){
        if($service->category->name === 'Catering'){
            return $service->cateringService->specifications;
        }

        if($service->category->name === 'Photography'){
            return $service->photographyService->specifications;
        }

        return null;

    }

    public function serviceShow(Service $service)
    {
        $service->load([
        'category',
        'vendor.user',
        'vendor.reviews.user.client',
        'cateringService',
        'photographyService',
        'vendor' => fn ($q) =>
            $q->withCount([
                'bookings as completed_services_count' =>
                    fn ($sub) => $sub->where('status', 'completed')
            ])
        ]);

        // manual transformation (same structure as index)
        $serviceData = [
            'id'            => $service->id,
            'name'          => $service->name,
            'description'   => $service->description,
            'price'         => $service->price,
            'image_url'     => $service->getFirstMediaUrl('images'),
            'category_name' => $service->category->name,
            'dateAdded'     => $service->created_at->format('Y-m-d'),
            'rating'        => $service->vendor->averageRating(),
            'is_available'  => $service->is_available,
            'catering_service' => $service->cateringService ?? null,
            'images'        => $service->getMedia('images')->map(fn ($media) => $media->getUrl()),
            'specifications'=> $this->specsChecker($service),
            'menuCategories'=> $service->cateringService->dishes ?? null,

            'vendor' => [
                'id'                 => $service->vendor->id,
                'email'              => $service->vendor->user->email,
                'user_id'            => $service->vendor->user_id,
                'full_name'          => $service->vendor->full_name,
                'is_approved'        => $service->vendor->is_approved,
                'business_name'      => $service->vendor->business_name,
                'description'        => $service->vendor->description,
                'location'           => $service->vendor->location,
                'contact_number'     => $service->vendor->contact_number,
                'created_at'         => $service->vendor->created_at,
                'updated_at'         => $service->vendor->updated_at,
                'completedServices'  => $service->vendor->completed_services_count,
                'avatar'             => $service->vendor->user->getFirstMediaUrl('images'),
                'website'            => 'to be added hehe',
            ],

            'reviews' => $service->vendor->reviews->map(fn($review) => [
                'id'      => $review->user->client->id,
                'name'    => $review->user->client->full_name,
                'rating'  => $review->rating,
                'date'    => $review->created_at->diffForHumans(),
                'comment' => $review->comment,
                'avatar'  => $review->user->getFirstMediaUrl('images') ?? null,
            ])
        ];

        return inertia('Client/Services/Show', [
            'service' => $serviceData
        ]);
    }




    public function events(){

        $user = auth()->user();

        $query = $user->events()->with(['bookings.service' => function ($q) {
            $q->with(['category', 'vendor']);
        }])->latest('created_at');

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

    public function showVendor(Vendor $vendor) {

        $vendor->load(['services', 'reviews', 'serviceCategories', 'user', 'bookings.event']);

        $vendorData = [
            'id' => $vendor->id,
            'name' => $vendor->business_name,
            'categories' => $vendor->serviceCategories->pluck('name'),
            'rating' => $vendor->averageRating(),
            'reviewCount' => $vendor->reviews()->count(),
            'location' => $vendor->location,
            'responseTime' => '2 hours',
            'completedEvents' => $vendor->getCompletedBookingsCount(),
            'available' => true, // ??
            'verified' => true, // ??
            'avatar' => $vendor->user->getFirstMediaUrl('images') ?? null,
            'phone' => $vendor->contact_number,
            'email' => $vendor->user->email,
            'website' => 'test.com', // ??
            'address' => $vendor->location,
            'serviceRadius' => 20, // ??,
            'description' => $vendor->description,
            'specialties' => ["Test", "Test", "Test", "Test", "Test", "Test"], // ??,
            'videoUrl' => "https://sample-videos.com/zip/10/mp4/SampleVideo_1280x720_1mb.mp4", // ??,
            'gallery' => $vendor->getMedia('images')->map(fn ($media) => [
                'url' => $media->getUrl()
            ]) ?? [],

            'packages' => $vendor->services->map(fn($service) => [
                'id' => $service->id,
                'name' => $service->name,
                'price' => $service->price,
                'features' => ["2 Main Speakers", "1 Wireless Microphone", "Basic Audio Mixing",] // ??,
            ]),

            'ratingBreakdown' => $vendor->ratingBreakdown(),

            'reviews' => $vendor->reviews->map(fn ($review) => [
                'id'      => $review->user->client->id,
                'name'    => $review->user->client->full_name,
                'rating'  => $review->rating,
                'date'    => $review->created_at->diffForHumans(),
                'comment' => $review->comment,
                'avatar'  => $review->user->getFirstMediaUrl('images') ?? null,
            ]),

            'calendarEvents' => $vendor->bookings()
            ->where('status', 'confirmed') // change here
            ->with('event') // make sure the event is loaded for start date
            ->get()
            ->map(fn($booking) => [
                'title' => 'Booked',
                'start' => $booking->event?->event_date?->format('Y-m-d'),
                'color' => '#ef4444', // green for confirmed
                'status' => $booking->status
            ]),




        ];

        // dd($vendorData);

        return inertia('Client/Vendor/Show', ['vendor' => $vendorData]);
    }

}