<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $vendor = auth()->user()->vendor;

        // Get base query with proper relationships
        $query = $vendor->bookings()
            ->with(['user', 'service', 'event']) // Load relationships
            ->select([
                'id',
                'user_id',
                'service_id',
                'event_id',
                'booking_date',
                'status',
                'created_at',
                'updated_at'
            ]);


        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('service', function ($serviceQuery) use ($search) {
                      $serviceQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('event', function ($eventQuery) use ($search) {
                      $eventQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('location', 'like', "%{$search}%");
                  });
            });
        }

        // Apply status filter
        if ($request->filled('status') && $request->get('status') !== 'all') {
            $query->where('status', $request->get('status'));
        }

        // Apply date range filter
        if ($request->filled('date_range') && $request->get('date_range') !== 'all') {
            $dateRange = $request->get('date_range');
            $today = Carbon::now()->startOfDay();

            switch ($dateRange) {
                case 'today':
                    $query->whereDate('booking_date', $today);
                    break;
                case 'week':
                    $query->whereBetween('booking_date', [
                        $today,
                        $today->copy()->addWeek()
                    ]);
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

        // Apply sorting
        $sortBy = $request->get('sort', 'date_desc');
        switch ($sortBy) {
            case 'date_desc':
                $query->orderBy('created_at', 'desc');
                break;
            case 'price_asc':
                $query->join('services', 'bookings.service_id', '=', 'services.id')
                      ->orderBy('services.price', 'asc')
                      ->select('bookings.*'); // Ensure we only select booking columns
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

        // Get paginated results
        $bookings = $query->paginate(20);

        // Transform the data for frontend
        $transformedBookings = $bookings->through(function ($booking) {
            $eventTime = 'Time TBD';
            if ($booking->event && $booking->event->event_time) {
                $eventTime = Carbon::parse($booking->event->event_time)->format('g:i A');
            }

            return [
                'id' => 'BK' . str_pad($booking->id, 3, '0', STR_PAD_LEFT),
                'client' => $booking->user->name ?? 'N/A',
                'client_email' => $booking->user->email ?? 'N/A',
                'service' => $booking->service->name ?? 'N/A',
                'event_name' => $booking->event->name ?? 'N/A',
                'event_location' => $booking->event->location ?? 'N/A',
                'date' => $booking->booking_date,
                'event_date' => $booking->event->event_date ?? $booking->booking_date,
                'time' => $eventTime,
                'status' => $booking->status,
                'price' => '₱' . number_format($booking->service->price ?? 0, 0),
                'contact' => $booking->user->email ?? 'N/A',
                'notes' => $booking->event->description ?? 'No additional notes',
                'raw_amount' => $booking->service->price ?? 0,
                'formatted_date' => Carbon::parse($booking->booking_date)->format('Y-m-d'),
                'created_at' => $booking->created_at,
                'updated_at' => $booking->updated_at,
                'raw_id' => $booking->id,
            ];
        });

        // Get booking statistics
        $stats = $this->getBookingStats($vendor);

        return Inertia::render('Vendor/Bookings/Index', [
            'bookings' => $transformedBookings,
            'stats' => $stats,
            'filters' => [
                'search' => $request->get('search', ''),
                'status' => $request->get('status', 'all'),
                'date_range' => $request->get('date_range', 'all'),
                'sort' => $request->get('sort', 'date_asc'),
            ]
        ]);
    }

    /**
     * Accept a pending booking
     */
    public function accept(Request $request, $id)
    {
        $vendor = auth()->user()->vendor;

        $booking = $vendor->bookings()
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        $booking->update([
            'status' => 'confirmed'
        ]);

        // Optional: Send notification to user
        // $this->sendBookingConfirmationNotification($booking);

        return back()->with('success', 'Booking has been confirmed successfully!');
    }

    /**
     * Cancel a pending booking (changed from decline to match your schema)
     */
    public function decline(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        $vendor = auth()->user()->vendor;

        $booking = $vendor->bookings()
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        $booking->update([
            'status' => 'cancelled'
        ]);

        // Optional: Send notification to user
        // $this->sendBookingCancellationNotification($booking, $request->get('reason'));

        return back()->with('success', 'Booking has been cancelled.');
    }

    /**
     * Mark a confirmed booking as completed
     */
    public function complete(Request $request, $id)
    {
        $vendor = auth()->user()->vendor;

        $booking = $vendor->bookings()
            ->where('id', $id)
            ->where('status', 'confirmed')
            ->firstOrFail();

        $booking->update([
            'status' => 'completed'
        ]);

        // Optional: Generate invoice or receipt
        // $this->generateInvoice($booking);

        return back()->with('success', 'Booking has been marked as completed!');
    }

    /**
     * Show booking details
     */
    public function show($id)
    {
        $vendor = auth()->user()->vendor;

        $booking = $vendor->bookings()
            ->with(['user', 'service', 'event', 'payment'])
            ->findOrFail($id);

        $bookingData = [
            'id' => 'BK' . str_pad($booking->id, 3, '0', STR_PAD_LEFT),
            'client' => [
                'name' => $booking->user->name,
                'email' => $booking->user->email,
                'phone' => $booking->user->phone ?? 'N/A',
            ],
            'service' => [
                'name' => $booking->service->name,
                'description' => $booking->service->description,
                'price' => $booking->service->price,
                'max_price' => $booking->service->max_price,
            ],
            'event' => [
                'name' => $booking->event->name,
                'location' => $booking->event->location,
                'event_date' => $booking->event->event_date,
                'event_time' => $booking->event->event_time,
                'description' => $booking->event->description,
            ],
            'booking_date' => $booking->booking_date,
            'status' => $booking->status,
            'created_at' => $booking->created_at,
            'updated_at' => $booking->updated_at,
            'payment' => $booking->payment ? [
                'amount' => $booking->payment->amount,
                'status' => $booking->payment->status,
                'payment_date' => $booking->payment->created_at,
            ] : null,
        ];

        return Inertia::render('Vendor/Bookings/Show', [
            'booking' => $bookingData
        ]);
    }

    /**
     * Bulk update booking statuses
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'booking_ids' => 'required|array',
            'booking_ids.*' => 'integer|exists:bookings,id',
            'action' => 'required|in:confirm,cancel,complete'
        ]);

        $vendor = auth()->user()->vendor;
        $bookingIds = $request->get('booking_ids');

        $bookings = $vendor->bookings()
            ->whereIn('id', $bookingIds)
            ->get();

        $updated = 0;

        foreach ($bookings as $booking) {
            $canUpdate = false;
            $newStatus = null;

            switch ($request->get('action')) {
                case 'confirm':
                    if ($booking->status === 'pending') {
                        $newStatus = 'confirmed';
                        $canUpdate = true;
                    }
                    break;
                case 'cancel':
                    if ($booking->status === 'pending') {
                        $newStatus = 'cancelled';
                        $canUpdate = true;
                    }
                    break;
                case 'complete':
                    if ($booking->status === 'confirmed') {
                        $newStatus = 'completed';
                        $canUpdate = true;
                    }
                    break;
            }

            if ($canUpdate) {
                $booking->update(['status' => $newStatus]);
                $updated++;
            }
        }

        $action = $request->get('action');
        $actionPast = $action === 'confirm' ? 'confirmed' : ($action === 'cancel' ? 'cancelled' : 'completed');

        return back()->with('success', "{$updated} booking(s) have been {$actionPast} successfully!");
    }

    /**
     * Get booking statistics for the vendor
     */
    private function getBookingStats($vendor)
    {
        $today = Carbon::now()->startOfDay();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();

        return [
            'total' => $vendor->bookings()->count(),
            'pending' => $vendor->bookings()->where('status', 'pending')->count(),
            'confirmed' => $vendor->bookings()->where('status', 'confirmed')->count(),
            'completed' => $vendor->bookings()->where('status', 'completed')->count(),
            'cancelled' => $vendor->bookings()->where('status', 'cancelled')->count(),
            'this_month' => $vendor->bookings()
                ->whereMonth('booking_date', $thisMonth->month)
                ->whereYear('booking_date', $thisMonth->year)
                ->count(),
            'today' => $vendor->bookings()
                ->whereDate('booking_date', $today)
                ->count(),
            'upcoming' => $vendor->bookings()
                ->where('booking_date', '>=', $today)
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
            'revenue_this_month' => $vendor->bookings()
                ->join('services', 'bookings.service_id', '=', 'services.id')
                ->where('bookings.status', 'completed')
                ->whereMonth('bookings.booking_date', $thisMonth->month)
                ->whereYear('bookings.booking_date', $thisMonth->year)
                ->sum('services.price'),
            'revenue_this_year' => $vendor->bookings()
                ->join('services', 'bookings.service_id', '=', 'services.id')
                ->where('bookings.status', 'completed')
                ->whereYear('bookings.booking_date', $thisYear->year)
                ->sum('services.price'),
        ];
    }

    /**
     * Export bookings to CSV
     */
    public function export(Request $request)
    {
        $vendor = auth()->user()->vendor;

        $bookings = $vendor->bookings()
            ->with(['user', 'service', 'event'])
            ->get();

        $filename = 'bookings-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Booking ID',
                'Client Name',
                'Client Email',
                'Service',
                'Event Name',
                'Event Location',
                'Booking Date',
                'Event Date',
                'Event Time',
                'Status',
                'Service Price',
                'Event Description',
                'Created At'
            ]);

            // CSV data
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    'BK' . str_pad($booking->id, 3, '0', STR_PAD_LEFT),
                    $booking->user->name ?? '',
                    $booking->user->email ?? '',
                    $booking->service->name ?? '',
                    $booking->event->name ?? '',
                    $booking->event->location ?? '',
                    $booking->booking_date,
                    $booking->event->event_date ?? '',
                    $booking->event->event_time ?? '',
                    ucfirst($booking->status),
                    $booking->service->price ?? 0,
                    $booking->event->description ?? '',
                    $booking->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
        $request->validate([
            'name'        => 'required|string|max:255',
            'location'    => 'required|string|max:255',
            'event_date'  => 'required|date',
            'event_time'  => 'nullable|date_format:H:i',
            'description' => 'nullable|string',
            'pax' => 'nullable',
            'dishes' => 'nullable|array',
            'vendors' => 'required|array',
            'vendors.*.id' => 'required|exists:services,id',

        ]);

        $event = Event::create([
            'user_id'     => auth()->id(),
            'name'        => $request->name,
            'location'    => $request->location,
            'event_date'  => $request->event_date,
            'event_time'  => $request->event_time,
            'description' => $request->description,
        ]);

        foreach ($request->vendors as $service) {
            $serviceModel = Service::findOrFail($service['id']);

            Booking::create([
                'vendor_id'    => $serviceModel->vendor_id,
                'user_id'      => auth()->id(),
                'event_id'     => $event->id,
                'service_id'   => $serviceModel->id,
                'booking_date' => $request->event_date,
                'catering_dishes' => $request->dishes ?? null,
                'pax' => $request->pax ?? null
            ]);
        }

        return redirect()->back()->with('success', 'Event and bookings created successfully!');
    }


    /**
     * Display the specified resource.
     */
    // public function show(Booking $booking)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        //
    }
}