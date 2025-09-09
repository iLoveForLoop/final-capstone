<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendor = auth()->user()->vendor;

        if (!$vendor) {
            abort(403, 'Not a vendor.');
        }

        $reviews = Review::with(['user.client', 'user.media', 'booking.event', 'booking.service'])
            ->where('vendor_id', $vendor->id)
            ->latest()
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,

                    'customer' => [
                        'name'     => $review->user->client->full_name ?? $review->user->name,
                        'avatar'   => $review->user->getFirstMediaUrl('images') ?? null,
                        'verified' => true, // you can set logic here if only "paid" bookings are verified
                    ],

                    'service' => $review->booking?->service?->name ?? 'Unknown Service',

                    'booking' => [
                        'id'         => 'BK-' . str_pad($review->booking?->id ?? 0, 6, '0', STR_PAD_LEFT),
                        'date'       => $review->booking?->created_at?->format('Y-m-d'),
                        'event_date' => $review->booking?->event?->event_date?->format('Y-m-d'),
                    ],

                    'rating' => $review->rating,
                    'title'  => $review->title ?? null, // add column if you want titles
                    'comment' => $review->comment,
                    'date'   => $review->created_at->format('Y-m-d'),
                    'status' => 'published', // you can implement moderation if needed
                    'helpful_votes' => $review->helpful_votes ?? 0, // add this field if you want upvotes
                    'response' => $review->response,

                    // 'photos' => $review->getMedia('review_photos')->map(fn ($m) => $m->getUrl())->toArray() ?? [],
                ];
            });

        return inertia('Vendor/Reviews/Index', [
            'reviews' => $reviews,
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
        // Validate incoming data
        // dd($request->vendor_id);
        $validated = $request->validate([
            'vendor_id'  => 'required|exists:vendors,id',
            'booking_id' => 'required|exists:bookings,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:500',
        ]);

        // Create review
        $review = Review::create([
            'vendor_id'  => $validated['vendor_id'],
            'booking_id' => $validated['booking_id'],
            'user_id'    => $request->user()->id,
            'rating'     => $validated['rating'],
            'comment'    => $validated['comment'] ?? null,
        ]);

        // return response()->json([
        //     'message' => 'Review submitted successfully.',
        //     'review'  => $review,
        // ], 201);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        //
    }
}
