<?php

namespace App\Http\Controllers;

use App\Mail\Vendor\VendorBookingCancelledMail;
use App\Models\Client;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $query = Service::with(['category', 'vendor', 'cateringService'])->where('is_available', true);
        $categories = ServiceCategory::all();

        $services = $query->paginate(6)->withQueryString()->through(fn($service) => [

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
            'catering_service' => $service->cateringService ?? null,
            'minimumGuests' => $service->cateringService->min_pax ?? null,
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

    public function services(Request $request)
    {
        // dd('hi');
        $categories = ServiceCategory::all();
        $query = Service::with(['category', 'vendor.reviews.user.client', 'cateringService', 'photographyService', 'vendor.user'])->where('is_available', true);

        // 🔹 Enhanced search filter - includes service name, description, and vendor business name
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                ->orWhere('description', 'like', '%' . $searchTerm . '%')
                ->orWhereHas('vendor', function ($vendorQuery) use ($searchTerm) {
                    $vendorQuery->where('business_name', 'like', '%' . $searchTerm . '%');
                });
            });
        }

        // 🔹 Multiple categories filter (updated to handle array)
        if ($request->filled('categories') && is_array($request->categories)) {
            $query->whereIn('service_category_id', $request->categories);
        }

        // 🔹 Price range filter
        if ($request->filled('price_range')) {
            switch ($request->price_range) {
                case 'under_5000':
                    $query->where('price', '<', 5000);
                    break;
                case '5000_15000':
                    $query->whereBetween('price', [5000, 15000]);
                    break;
                case '15000_30000':
                    $query->whereBetween('price', [15000, 30000]);
                    break;
                case '30000_50000':
                    $query->whereBetween('price', [30000, 50000]);
                    break;
                case 'over_50000':
                    $query->where('price', '>', 50000);
                    break;
            }
        }

        // 🔹 Rating filter - using Eloquent relationship
        if ($request->filled('rating')) {
            $minRating = (float) $request->rating;
            $query->whereHas('vendor', function ($vendorQuery) use ($minRating) {
                $vendorQuery->whereHas('reviews', function ($reviewQuery) use ($minRating) {
                    $reviewQuery->havingRaw('AVG(rating) >= ?', [$minRating]);
                });
            });
        }

        // Get the base collection first, then sort in PHP for complex sorting
        $services = $query->get();

        // 🔹 Sorting - done in PHP to avoid complex SQL joins
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'rating':
                    $services = $services->sortByDesc(function ($service) {
                        return $service->vendor->averageRating() ?? 0;
                    });
                    break;
                case 'reviews':
                    $services = $services->sortByDesc(function ($service) {
                        return $service->vendor->reviews->count();
                    });
                    break;
                case 'price_low':
                    $services = $services->sortBy('price');
                    break;
                case 'price_high':
                    $services = $services->sortByDesc('price');
                    break;
                case 'relevance':
                default:
                    // For search queries, sort by relevance based on search term matches
                    if ($request->filled('search')) {
                        $searchTerm = strtolower($request->search);
                        $services = $services->sortByDesc(function ($service) use ($searchTerm) {
                            $score = 0;

                            // Exact match in service name gets highest score
                            if (stripos($service->name, $searchTerm) === 0) {
                                $score += 100;
                            } elseif (stripos($service->name, $searchTerm) !== false) {
                                $score += 50;
                            }

                            // Business name matches get medium score
                            if (stripos($service->vendor->business_name ?? '', $searchTerm) === 0) {
                                $score += 75;
                            } elseif (stripos($service->vendor->business_name ?? '', $searchTerm) !== false) {
                                $score += 25;
                            }

                            // Description matches get lower score
                            if (stripos($service->description ?? '', $searchTerm) !== false) {
                                $score += 10;
                            }

                            return $score;
                        });
                    } else {
                        // Default to newest first when no search term
                        $services = $services->sortByDesc('created_at');
                    }
                    break;
            }
        } else {
            // Default sort logic
            if ($request->filled('search')) {
                // Apply relevance sorting for search queries even when sort is not specified
                $searchTerm = strtolower($request->search);
                $services = $services->sortByDesc(function ($service) use ($searchTerm) {
                    $score = 0;

                    if (stripos($service->name, $searchTerm) === 0) {
                        $score += 100;
                    } elseif (stripos($service->name, $searchTerm) !== false) {
                        $score += 50;
                    }

                    if (stripos($service->vendor->business_name ?? '', $searchTerm) === 0) {
                        $score += 75;
                    } elseif (stripos($service->vendor->business_name ?? '', $searchTerm) !== false) {
                        $score += 35;
                    }

                    // Location scoring for single field
                    if (stripos($service->vendor->location ?? '', $searchTerm) !== false) {
                        $score += 40;
                    }

                    if (stripos($service->description ?? '', $searchTerm) !== false) {
                        $score += 10;
                    }

                    return $score;
                });
            } else {
                $services = $services->sortByDesc('created_at');
            }
        }

        // Convert to array and paginate manually
        $servicesArray = $services->values()->map(fn($service) => [
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
            'catering_service' => $service->cateringService ?? null,
            'minimumGuests' => $service->cateringService->min_pax ?? null,

        ])->toArray();

        // Manual pagination
        $perPage = 10;
        $currentPage = $request->get('page', 1);
        $total = count($servicesArray);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedItems = array_slice($servicesArray, $offset, $perPage);

        $paginatedServices = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Format for frontend
        $services = (object) [
            'data' => $paginatedServices->items(),
            'total' => $paginatedServices->total(),
            'current_page' => $paginatedServices->currentPage(),
            'last_page' => $paginatedServices->lastPage(),
            'per_page' => $paginatedServices->perPage(),
            'from' => $paginatedServices->firstItem(),
            'to' => $paginatedServices->lastItem(),
        ];

        // 🔹 Pass filters back to Inertia
        $filters = $request->only(['search', 'categories', 'price_range', 'rating', 'sort']);

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
        // dd('hi');
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
            'specifications'=> $service->specifications,
            'menuCategories'=> $service->cateringService->dishes ?? null,
            'minimumGuests' => $service->cateringService->min_pax ?? null,
            'is_favorite' => $service->isFavoritedBy(),

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
                'avatar'             => $service->vendor->user->getFirstMediaUrl('avatar'),
                'website'            => 'to be added hehe',
            ],

            'reviews' => $service->vendor->reviews->map(fn($review) => [
                'id'      => $review->user->client->id,
                'name'    => $review->user->client->full_name,
                'rating'  => $review->rating,
                'date'    => $review->created_at->diffForHumans(),
                'comment' => $review->comment,
                'avatar'  => $review->user->getFirstMediaUrl('avatar') ?? null,
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

        $categories = ServiceCategory::all();

        $events = $query->paginate(10)->withQueryString()->through(fn ($event) => [
            'id' => 'EVT' . str_pad($event->id, 3, '0', STR_PAD_LEFT),
            'title' => $event->name,
            'start' => $event->start,
            'location' => $event->location,
            'description' => $event->description,
            'status' => $event->status,
            'services' => $event->bookings->map(fn ($booking) => [
                'name' => $booking->service->category->name,
                'provider' => $booking->service->vendor->business_name,
                'status' => $booking->status
            ]),
            'createdDate' => $event->created_at

        ]);

        return inertia('Client/Events/Index', compact('events', 'categories'));
    }

    public function showVendor(Vendor $vendor) {

        $vendor->load(['services', 'reviews', 'serviceCategories', 'user', 'bookings.event']);
        $vendorData = [
            'id' => $vendor->id,
            'user_id' => $vendor->user->id,
            'name' => $vendor->business_name,
            'categories' => $vendor->serviceCategories->pluck('name'),
            'rating' => $vendor->averageRating(),
            'reviewCount' => $vendor->reviews()->count(),
            'location' => $vendor->location ?? null,
            'latitude' => $vendor->latitude ?? null,
            'longitude' => $vendor->longitude,
            'responseTime' => '2 hours',
            'completedEvents' => $vendor->getCompletedBookingsCount(),
            'available' => true, // ??
            'verified' => true, // ??
            'avatar' => $vendor->user->getFirstMediaUrl('avatar') ?? null,
            'phone' => $vendor->contact_number,
            'email' => $vendor->user->email,
            'website' => 'test.com', // ??
            'address' => $vendor->location,
            'serviceRadius' => 20, // ??,
            'description' => $vendor->description,
            'specialties' => $vendor->specialties, // ??,
            'videoUrl' => $vendor->getFirstMediaUrl('showcaseVideos') ?? null, // ??,
            'gallery' => $vendor->getMedia('portfolioImages')->map(fn ($media) => [
                'url' => $media->getUrl()
            ]) ?? [],

            'packages' => $vendor->services->map(fn($service) => [
                'id' => $service->id,
                'name' => $service->name,
                'price' => $service->price,
                'features' => $service->specifications // ??,
            ]),

            'legalDocuments' => $vendor->getMedia('permitFiles')->map(fn($media) => [
                'url' => $media->getUrl()
            ]) ?? null,

            'ratingBreakdown' => $vendor->ratingBreakdown(),

            'reviews' => $vendor->reviews->map(fn ($review) => [
                'id'      => $review->user->client->id,
                'name'    => $review->user->client->full_name,
                'rating'  => $review->rating,
                'date'    => $review->created_at->diffForHumans(),
                'comment' => $review->comment,
                'avatar'  => $review->user->getFirstMediaUrl('avatar') ?? null,
                'response' => $review->response ?? null,
                'responded_at' => $review->responded_at ? $review->responded_at->diffForHumans() : null
            ]),

            'calendarEvents' => $vendor->bookings()
            ->where('status', 'confirmed') // change here
            ->with('event') // make sure the event is loaded for start date
            ->get()
            ->map(fn($booking) => [
                'title' => 'Booked',
                'start' => $booking->event?->event_date?->format('Y-m-d'),
                // 'color' => '#ef4444',
                'status' => $booking->status
            ]),




        ];

        // dd($vendorData);

        return inertia('Client/Vendor/Show', ['vendor' => $vendorData]);
    }

    public function searchPage(Request $request){

        $categories = ServiceCategory::all();




        return inertia('Client/Search/Index', compact('categories'));
    }



    public function getVendorServices(Vendor $vendor)
    {
        $vendor->load([
            'services' => function ($query) {
                $query->where('is_available', true)
                    ->with(['category', 'cateringService', 'vendor']);
            },
        ]);

        $services = $vendor->services->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'price' => $service->price,
                'image_url' => $service->getFirstMediaUrl('images'),
                'category_name' => $service->category->name ?? null,
                'dateAdded' => $service->created_at->format('Y-m-d'),
                'vendor' => $service->vendor,
                'rating' => $service->vendor->averageRating(),
                'is_available' => $service->is_available,
                'catering_service' => $service->cateringService ?? null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $services,
        ]);
    }


    public function cancelBooking(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        // dd($request->comment);
        // dd($request->comment);

        $user = auth()->user();

        $booking = $user->bookings()
            ->where('id', $id)
            // ->where('status', 'pending')
            ->firstOrFail();

        $booking->update([
            'status' => 'cancelled'
        ]);

        // Optional: Send notification to user
        // $this->sendBookingCancellationNotification($booking, $request->get('reason'));

        Mail::to($booking->service->vendor->user->email)->queue(new VendorBookingCancelledMail($booking->id, $request->reason, $request->comment));

        return back()->with('success', 'Booking has been cancelled.');
    }










    //BOOKING
    public function bookings(Request $request){
    $user = auth()->user();

    // ✅ Ensure only clients can access this controller
    if (!$user->hasRole('client')) {
        return back()->with('error', 'Unauthorized.');
    }

    $categories = ServiceCategory::all();

    // 🔹 Client bookings query
    $query = $user->bookings()
        ->with([
            'service.category',
            'service.cateringService',
            'event',
            'service.vendor',
            'review',
            'service.vendor.user'
        ]);

    // 🔹 Search filter
    if ($request->filled('search')) {
        $search = $request->get('search');
        $query->where(function ($q) use ($search) {
            $q->where('id', 'like', "%{$search}%")
                ->orWhereHas('service', fn($sq) =>
                    $sq->where('name', 'like', "%{$search}%"))
                ->orWhereHas('event', fn($eq) =>
                    $eq->where('name', 'like', "%{$search}%")
                       ->orWhere('location', 'like', "%{$search}%"));
        });
    }

    // 🔹 Status filter
    if ($request->filled('status') && $request->get('status') !== 'all') {
        $query->where('status', $request->get('status'));
    }

    // Category filter
    if ($request->filled('category') && $request->get('category') !== 'all') {
        $query->whereHas('service.category', function ($q) use ($request) {
            $q->where('id', $request->get('category'));
        });
    }

    // 🔹 Date range filter
    if ($request->filled('date_range') && $request->get('date_range') !== 'all') {
        $today = Carbon::now()->startOfDay();
        switch ($request->get('date_range')) {
            case 'today':
                $query->whereDate('booking_date', $today);
                break;
            case 'week':
                $query->whereBetween('booking_date', [$today, $today->copy()->addWeek()]);
                break;
            case 'month':
                $query->whereMonth('booking_date', $today->month)
                    ->whereYear('booking_date', $today->year);
                break;
            case 'upcoming':
                $query->where('booking_date', '>=', $today);
                break;
            case 'past':
                $query->where('booking_date', '<', $today);
                break;
        }
    }

    // 🔹 Sorting
    $sortBy = $request->get('sort', 'date_desc');
    switch ($sortBy) {
        case 'date_desc':
            $query->orderBy('created_at', 'desc');
            break;
        case 'price_asc':
            $query->join('services', 'bookings.service_id', '=', 'services.id')
                ->orderBy('services.price', 'asc')
                ->select('bookings.*');
            break;
        case 'price_desc':
            $query->join('services', 'bookings.service_id', '=', 'services.id')
                ->orderBy('services.price', 'desc')
                ->select('bookings.*');
            break;
        case 'date_asc':
        default:
            $query->orderBy('created_at', 'asc');
            break;
    }

    // 🔹 Get ALL bookings for statistics (unfiltered)
    $allBookings = $user->bookings()->with(['service'])->get();

    // 🔹 Calculate statistics from ALL bookings
    $bookingStats = [
        'total' => $allBookings->count(),
        'confirmed' => $allBookings->where('status', 'confirmed')->count(),
        'pending' => $allBookings->where('status', 'pending')->count(),
        'completed' => $allBookings->where('status', 'completed')->count(),
        'cancelled' => $allBookings->where('status', 'cancelled')->count(),
        'total_spent' => $allBookings->where('status', 'completed')->sum('service.price')
    ];

    // 🔹 Paginate + transform filtered results
    $bookings = $query->paginate(20)->withQueryString()->through(function ($booking) {
        $eventTime = $booking->event && $booking->event->event_time
            ? Carbon::parse($booking->event->event_time)->format('g:i A')
            : 'Time TBD';

        $is_per_pax = $booking->service->cateringService
            ? ($booking->service->cateringService->price ?? false) !== ($booking->service->cateringService->package_price ?? null)
            : false;

        return [
            'f_id' => 'BK' . str_pad($booking->id, 3, '0', STR_PAD_LEFT),
            'id' => $booking->id,
            'event_name' => $booking->event->name ?? 'N/A',
            'event_location' => $booking->event->location ?? 'N/A',
            'date' => $booking->booking_date,
            'event_date' => $booking->event->event_date ?? $booking->booking_date,
            'time' => $eventTime,
            'status' => $booking->status,
            'price' => '₱' . number_format($booking->service->price ?? 0, 0),
            'notes' => $booking->event->description ?? 'No additional notes',
            'raw_amount' => $booking->service->price ?? 0,
            'formatted_date' => Carbon::parse($booking->booking_date)->format('Y-m-d'),
            'created_at' => $booking->created_at,
            'service' => $booking->service,
            'category' => $booking->service->category,
            'is_per_pax' => $is_per_pax,
            'service_image' => $booking->service->getFirstMediaUrl('images'),
            'vendor' => $booking->service->vendor,
            'vendor_avatar' => $booking->service->vendor->getFirstMediaUrl('images') ?? null,
            'vendor_rating' => $booking->service->vendor->averageRating(),
            'can_review' => !$booking->hasReviewFrom(auth()->id()),
            'review' => $booking->review ? [
                'id' => $booking->review->id,
                'comment' => $booking->review->comment,
                'reviewDate' => $booking->review->created_at,
                'serviceName' => $booking->service->name,
                'serviceProvider' => $booking->service->vendor->business_name,
                'rating' => $booking->review->rating,
                'vendorResponse' => [
                    'message' => $booking->review->response,
                    'date' => $booking->review->responded_at
                ]
            ] : null
        ];
    });

    // 🔹 Return client view
    return inertia('Client/Bookings/Index', [
        'bookings' => $bookings,
        'categories' => $categories,
        'booking_stats' => $bookingStats, // Add stats here
        'filters' => [
            'search' => $request->get('search', ''),
            'category' => $request->get('category', 'all'),
            'status' => $request->get('status', 'all'),
            'date_range' => $request->get('date_range', 'all'),
        ]
    ]);
}


    public function profile(Request $request){
        return Inertia::render('Client/Profile/Index', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }



}