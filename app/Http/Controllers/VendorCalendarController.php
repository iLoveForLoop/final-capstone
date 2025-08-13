<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VendorCalendarController extends Controller
{
    //
    /**
     * Display the vendor calendar page
     */
    public function index(Request $request)
    {
        $vendor = auth()->user()->vendor;

        // Get current month or specified month
        $month = $request->get('month', now()->format('Y-m'));
        $date = Carbon::createFromFormat('Y-m', $month);

        // Get bookings for the current month (extend range for better UX)
        $startDate = $date->copy()->startOfMonth()->subDays(7);
        $endDate = $date->copy()->endOfMonth()->addDays(7);

        // Fetch bookings with related data
        $bookings = $vendor->bookings()
            ->select([
                'bookings.id',
                'bookings.status',
                'events.event_date',
                'services.name as service_name',
                'users.name as client_name'
            ])
            ->join('events', 'bookings.event_id', '=', 'events.id')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->whereBetween('events.event_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereIn('bookings.status', ['pending', 'confirmed', 'completed', 'cancelled']) // Exclude cancelled
            ->orderBy('events.event_date', 'asc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'status' => $booking->status,
                    'event_date' => $booking->event_date,
                    'service_name' => $booking->service_name,
                    'client_name' => $booking->client_name,
                ];
            });

        // Calculate booking statistics
        $bookingStats = $this->getBookingStats($vendor);

        return Inertia::render('Vendor/Calendar/Index', [
            'bookings' => $bookings,
            'bookingStats' => $bookingStats,
            'currentMonth' => $month,
        ]);
    }

    /**
     * Get booking statistics for the vendor
     */
    private function getBookingStats($vendor)
    {
        $now = Carbon::now();

        // Total bookings (all time, excluding cancelled)
        $totalBookings = $vendor->bookings()
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->count();

        // This month's bookings
        $thisMonthBookings = $vendor->bookings()
            ->join('events', 'bookings.event_id', '=', 'events.id')
            ->whereYear('events.event_date', $now->year)
            ->whereMonth('events.event_date', $now->month)
            ->whereIn('bookings.status', ['pending', 'confirmed', 'completed'])
            ->count();

        // This week's bookings
        $thisWeekBookings = $vendor->bookings()
            ->join('events', 'bookings.event_id', '=', 'events.id')
            ->whereBetween('events.event_date', [$now->startOfWeek(), $now->endOfWeek()])
            ->whereIn('bookings.status', ['pending', 'confirmed', 'completed'])
            ->count();

        // Upcoming bookings (next 30 days)
        $upcomingBookings = $vendor->bookings()
            ->join('events', 'bookings.event_id', '=', 'events.id')
            ->whereBetween('events.event_date', [$now->format('Y-m-d'), $now->addDays(30)->format('Y-m-d')])
            ->whereIn('bookings.status', ['pending', 'confirmed'])
            ->count();

        return [
            'total' => $totalBookings,
            'thisMonth' => $thisMonthBookings,
            'thisWeek' => $thisWeekBookings,
            'upcoming' => $upcomingBookings,
        ];
    }

    /**
     * Get bookings for a specific month (AJAX endpoint)
     */
    public function getBookingsForMonth(Request $request)
    {
        $vendor = auth()->user()->vendor;
        $month = $request->get('month', now()->format('Y-m'));

        try {
            $date = Carbon::createFromFormat('Y-m', $month);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid month format'], 400);
        }

        $startDate = $date->copy()->startOfMonth()->subDays(7);
        $endDate = $date->copy()->endOfMonth()->addDays(7);

        $bookings = $vendor->bookings()
            ->select([
                'bookings.id',
                'bookings.status',
                'events.event_date',
                'events.event_time',
                'services.name as service_name',
                'services.price',
                'users.name as client_name'
            ])
            ->join('events', 'bookings.event_id', '=', 'events.id')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->whereBetween('events.event_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereIn('bookings.status', ['pending', 'confirmed', 'completed'])
            ->orderBy('events.event_date', 'asc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'status' => $booking->status,
                    'event_date' => $booking->event_date,
                    'event_time' => $booking->event_time,
                    'service_name' => $booking->service_name,
                    'client_name' => $booking->client_name,
                    'price' => $booking->price,
                    'formatted_price' => '₱' . number_format($booking->price, 0),
                ];
            });

        return response()->json([
            'bookings' => $bookings,
            'month' => $month
        ]);
    }

    /**
     * Get booking details for a specific day (AJAX endpoint)
     */
    public function getBookingsForDay(Request $request)
    {
        $vendor = auth()->user()->vendor;
        $date = $request->get('date');

        if (!$date || !Carbon::hasFormat($date, 'Y-m-d')) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        $bookings = $vendor->bookings()
            ->select([
                'bookings.id',
                'bookings.status',
                'events.event_date',
                'events.event_time',
                'events.event_location',
                'services.name as service_name',
                'services.price',
                'users.name as client_name',
                'users.email as client_email'
            ])
            ->join('events', 'bookings.event_id', '=', 'events.id')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->where('events.event_date', $date)
            ->whereIn('bookings.status', ['pending', 'confirmed', 'completed'])
            ->orderBy('events.event_time', 'asc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'status' => $booking->status,
                    'event_date' => $booking->event_date,
                    'event_time' => $booking->event_time ?? '12:00 PM',
                    'event_location' => $booking->event_location,
                    'service_name' => $booking->service_name,
                    'client_name' => $booking->client_name,
                    'client_email' => $booking->client_email,
                    'price' => $booking->price,
                    'formatted_price' => '₱' . number_format($booking->price, 0),
                ];
            });

        return response()->json([
            'bookings' => $bookings,
            'date' => $date,
            'count' => $bookings->count()
        ]);
    }
}
