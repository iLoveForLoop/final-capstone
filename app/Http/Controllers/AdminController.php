<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Report;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(){

        $users = User::whereDoesntHave('vendor', function ($query) {
            $query->where('is_approved', false);
        })->get();
        $bookings = Booking::all();
        $labels = [];
        $data = [];

        $vendors = Vendor::where('is_approved', true);
        $pending_applications = Vendor::with('serviceCategories')
            ->where('is_approved', false)
            ->get();



        // FOR MYSQL
        $monthlyBookings = DB::table('bookings')
        ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
        ->whereYear('created_at', now()->year)
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->orderBy('month')
        ->get();


        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->format('F');
            $matching = $monthlyBookings->firstWhere('month', $i);
            $data[] = $matching ? $matching->total : 0;
        }
        // END FOR MYSQL

        // FOR SQLITE
        // $monthlyBookings = DB::table('bookings')
        // ->selectRaw("strftime('%m', created_at) as month, COUNT(*) as total")
        // ->whereRaw("strftime('%Y', created_at) = ?", [now()->year])
        // ->groupByRaw("strftime('%m', created_at)")
        // ->orderBy('month')
        // ->get();

        // for ($i = 1; $i <= 12; $i++) {
        //     $monthStr = str_pad($i, 2, '0', STR_PAD_LEFT); // ensures '01', '02', etc.
        //     $labels[] = Carbon::create()->month($i)->format('F');
        //     $matching = $monthlyBookings->firstWhere('month', $monthStr);
        //     $data[] = $matching ? $matching->total : 0;
        // }
        // END FOR MYSQLITE


        $topVendors = Vendor::withAvg('reviews', 'rating')
        ->where('is_approved', true)
        ->withCount('bookings')
        ->with(['serviceCategories', 'user']) // get user.name, user.email if needed
        ->orderByDesc('reviews_avg_rating')
        ->take(5)
        ->get();


        $stats = [
            'total_users' => $users->count(),
            'total_vendors' => $vendors->count(),
            'pending_applications' => $pending_applications->count(),
            'pending_vendors' => $pending_applications,
            'total_bookings' => $bookings->count(),
            'labels' => $labels,
            'data' => $data,
            'top_vendors' => $topVendors
        ];





        return inertia('Admin/Index', compact('stats'));
    }


    public function servicesPage(Request $request)
    {
        $query = Service::with(['vendor', 'category', 'cateringService', 'media']);
        $categories = ServiceCategory::all();
        $vendors = Vendor::with('serviceCategories')
            ->where('is_approved', true)
            ->get();


        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }


        if ($request->filled('category') && $request->category != 'all') {
            $query->where('service_category_id', $request->category);
        }


        if ($request->filled('vendor') && $request->vendor != 'all') {
            $query->where('vendor_id', $request->vendor);
        }

        $services = $query->paginate(5)->withQueryString();



        return inertia('Admin/Services/Index', compact('services', 'categories', 'vendors'));
    }

    // public function addService(Request $request) {
    //     $request->validate([
    //         'vendor_id' => 'required|exists:vendors,id',
    //         'service_category_id' => 'required|exists:service_categories,id',
    //         'name' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'price' => 'required|numeric|min:0',
    //         'is_available' => 'boolean',
    //         'image' => 'nullable|image|max:2048'
    //     ]);

    //     $service = Service::create([
    //         'vendor_id' => $request->vendor_id,
    //         'service_category_id' => $request->service_category_id,
    //         'name' => $request->name,
    //         'description' => $request->description,
    //         'price' => $request->price,
    //         'is_available' => $request->is_available,
    //     ]);

    //     if($request->hasFile('image')){
    //         $service->addMediaFromRequest('image')->toMediaCollection('images', 'public');
    //     }

    //     return redirect()->back()->with('success', 'Service Added Successfully');
    // }


    public function bookingsPage(Request $request)
    {
        $query = Booking::with(['user', 'service.vendor', 'event'])
            ->latest();

        // SEARCH FILTER
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('user', function($subQuery) use ($request) {
                    $subQuery->where('name', 'like', '%'.$request->search.'%')
                            ->orWhere('email', 'like', '%'.$request->search.'%');
                })
                ->orWhereHas('service', function($subQuery) use ($request) {
                    $subQuery->where('name', 'like', '%'.$request->search.'%');
                });
            });
        }

        // STATUS FILTER
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $bookings = $query->paginate(5)->withQueryString();

        return inertia('Admin/Bookings/Index', [
            'bookings' => $bookings,
            'filters' => [
                'status' => $request->get('status', 'all'),
                'search' => $request->get('search', ''),
            ],
        ]);
    }

    public function bookingShow($id){
        $booking = Booking::with(['user', 'service.vendor', 'event', 'service.category'])->findOrFail($id);

        return inertia('Admin/Bookings/Show', [
            'booking' => $booking,
        ]);
    }


    public function reviewsPage(Request $request){
        $search = $request->get('search');

        $reviews = Review::with(['user', 'vendor'])
            ->when($search, function ($query, $search) {
                $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('vendor', fn($q) => $q->where('business_name', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return inertia('Admin/Reviews/Index', [
            'reviews' => $reviews,
            'filters' => [
                'search' => $search
            ],
        ]);
    }

    public function reportsPage(Request $request){
        // Server-side filtering and pagination
        $query = Report::with(['reporter', 'reported', 'reporter.vendor',
            'reported.vendor',])
            ->latest();

        // Status filter
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('reporter', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('reported', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $reports = $query->paginate(5)->withQueryString();

        return inertia('Admin/Reports/Index', [
            'reports' => $reports,
            'filters' => [
                'status' => $request->status ?? 'all',
                'search' => $request->search ?? '',
            ]
        ]);
    }

    public function updateStatus(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,resolved,dismissed'
        ]);

        $report->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Report status updated successfully.');
    }

    // public function show(Report $report)
    // {
    //     $report->load(['reporter', 'reported']);

    //     return inertia('Admin/Reports/Show', [
    //         'report' => $report
    //     ]);
    // }

    public function settingsPage() {
        return inertia('Admin/Settings/Index');
    }


    public function updateGeneralService(Request $request, $id){

        $service = Service::with(['media'])->findOrFail($id);

        $validated = $request->validate([
            'service_category_id' => 'required|integer|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gt:price',

            'specifications' => 'nullable|array',
            'specifications.*' => 'string|max:255',


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

            return back()->with('success', 'Photography service updated successfully');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error updating photography service: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update service. Please try again.'])->withInput();
        }
    }

    public function updateCateringService(Request $request, $id){

        $service = Service::with(['cateringService', 'media'])->findOrFail($id);

        // Validation logic similar to store method
        if(!$request->price){
            $request->validate([
                'package_price' => 'required|numeric'
            ]);
        }

        if(!$request->package_price){
            $request->validate([
                'price' => 'required|numeric'
            ]);
        }

        if(!$request->package_price && !$request->price){
            $request->validate([
                'price' => 'required|numeric',
                'package_price' => 'required|numeric'
            ]);
        }

        $validated = $request->validate([
            'service_category_id' => 'required|integer|exists:service_categories,id',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'max_price' => 'nullable|numeric',

            // Catering specific
            'min_pax' => 'required|integer',
            'max_pax' => 'required|integer',
            'lead_time_days' => 'nullable|integer',
            'service_area' => 'nullable|array',
            'is_customizable' => 'nullable|boolean',
            'delivery_fee' => 'nullable|numeric',
            'buffet_type' => 'nullable|string',
            'specifications' => 'nullable|array',
            'dishes' => 'required|array',
            'dish_selection_limits' => 'nullable|array',
            'notes' => 'nullable|string',

            // Images validation
            'cover_images' => 'nullable|array',
            'cover_images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'deleted_images' => 'nullable|array',
            'deleted_images.*' => 'integer|exists:media,id',
        ]);

        // Additional validation for multiple images
        $currentImageCount = $service->media->count();
        $deletedCount = is_array($request->deleted_images) ? count($request->deleted_images) : 0;
        $newImageCount = $request->hasFile('cover_images') ? count($request->file('cover_images')) : 0;
        $totalAfterUpdate = $currentImageCount - $deletedCount + $newImageCount;

        if ($totalAfterUpdate > 5) {
            return back()->withErrors(['cover_images' => 'Total images cannot exceed 5. Please remove some existing images first.'])->withInput();
        }

        DB::beginTransaction();

        try {
            // Step 1: Update the general service
            $service->update([
                'service_category_id' => $validated['service_category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $request->price ?? $request->package_price,
                'max_price' => $validated['max_price'] ?? null,
                'specifications' => $validated['specifications'] ?? [],
            ]);

            // Step 2: Update the related catering service
            $service->cateringService->update([
                'name' => $validated['name'],
                'min_pax' => $validated['min_pax'],
                'max_pax' => $validated['max_pax'],
                'price' => $request->price ?? $request->package_price,
                'package_price' => $request->package_price ?? null,
                'lead_time_days' => $validated['lead_time_days'] ?? 3,
                'service_area' => $validated['service_area'] ?? [],
                'is_customizable' => $validated['is_customizable'] ?? false,
                'delivery_fee' => $validated['delivery_fee'] ?? null,
                'buffet_type' => $validated['buffet_type'] ?? null,
                'specifications' => $validated['specifications'] ?? [],
                'dishes' => $validated['dishes'] ?? [],
                'dish_selection_limits' => $validated['dish_selection_limits'] ?? [],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Step 3: Handle image deletions
            if ($request->has('deleted_images') && is_array($request->deleted_images)) {
                foreach ($request->deleted_images as $mediaId) {
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
                    $service->addMediaFromRequest("cover_images.{$index}")
                        ->usingFileName(uniqid() . '.' . $image->getClientOriginalExtension())
                        ->toMediaCollection('images', 'public');
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
                $firstMedia->save();
            }

            DB::commit();

            return back()->with('success', 'Service updated successfully');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error updating catering service: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update service. Please try again.'])->withInput();
        }
    }




}