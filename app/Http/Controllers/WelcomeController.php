<?php

namespace App\Http\Controllers;

use App\Helpers\RedirectHelper;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

class WelcomeController extends Controller
{
    public function index(){

        if (auth()->check()) {
            return RedirectHelper::redirectBasedOnRole(auth()->user());
        }
        // dd('hi');
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
            'catering_service' => $service->cateringService ?? null
        ]);

        return inertia('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'services' => $services,
            'categories' => $categories
        ]);
    }

    public function servicesPage(Request $request){
        // dd('hi');
        $categories = ServiceCategory::all();
        $query = Service::with(['category', 'vendor.reviews.user.client', 'cateringService', 'photographyService', 'vendor.user']);

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
            'catering_service' => $service->cateringService ?? null

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

        return inertia('Services', compact('filters', 'services', 'categories'));
    }

    public function thankYou(Request $request){

        // $request->registered = true;
        // dd($request->registered);

        if($request->registered){
            return inertia('ThankYou');
        }

        return redirect('/');

    }
}
