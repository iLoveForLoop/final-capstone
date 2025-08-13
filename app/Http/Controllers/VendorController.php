<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Services\NotificationService;
use App\Models\Notification;

class VendorController extends Controller
{

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendor = auth()->user()->vendor;

        $totalBookings = $vendor->bookings()->count();
        $pendingBookings = $vendor->bookings()->where('status', 'pending')->count();
        $completedBookings = $vendor->bookings()->where('status', 'completed')->get();
        $ongoingBookings = $vendor->bookings()->where('status', 'confirmed')->count();

        $totalRevenue = 0;

        foreach ($completedBookings as $cbook) {
            $totalRevenue += optional($cbook->service)->price ?? 0;
        }

        // Get chart data for the last 6 months
        $chartData = $this->getOptimizedChartData($vendor);

        // Get upcoming events (next 5 confirmed bookings)
        $upcomingEvents = $this->getUpcomingEvents($vendor);


        // Get recent bookings (last 5 bookings)
        $recentBookings = $this->getRecentBookings($vendor);

        // Get notifications (last 5 notifications)
        $notifications = $this->getNotifications($vendor);

        // Get unread notification count
        $unreadNotificationCount = $this->notificationService->getUnreadCountForVendor($vendor->id);



        $stats = [
            'total_bookings' => $totalBookings,
            'pending_bookings' => $pendingBookings,
            'completed_bookings' => $completedBookings->count(),
            'ongoing_bookings' => $ongoingBookings,
            'total_revenue' => $totalRevenue
        ];

        return inertia('Vendor/Index', compact('stats', 'chartData', 'upcomingEvents', 'recentBookings', 'notifications', 'unreadNotificationCount'));
    }


    //NOTIFICATIONS

    private function getNotifications($vendor)
    {
        return $vendor->notifications()
            ->orderBy('created_at', 'desc')
            ->take(5) // Show only 5 latest notifications on dashboard
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'text' => $notification->message,
                    'time' => $notification->time_ago,
                    'read' => $notification->is_read,
                    'type' => $notification->type,
                    'priority' => $notification->priority,
                    'action_url' => $notification->action_url,
                    'icon' => $this->getNotificationIcon($notification->type),
                    'color' => $this->getNotificationColor($notification->type, $notification->priority)
                ];
            });
    }

    private function getNotificationIcon($type)
    {
        return match($type) {
            Notification::TYPE_BOOKING_RECEIVED => 'calendar-plus',
            Notification::TYPE_BOOKING_COMPLETED => 'check-circle',
            Notification::TYPE_BOOKING_CANCELLED => 'x-circle',
            Notification::TYPE_BOOKING_CONFIRMED => 'calendar-check',
            Notification::TYPE_SERVICE_UPDATED => 'settings',
            Notification::TYPE_PAYMENT_RECEIVED => 'credit-card',
            Notification::TYPE_REVIEW_RECEIVED => 'star',
            Notification::TYPE_SYSTEM_ALERT => 'alert-circle',
            default => 'bell'
        };
    }

    private function getNotificationColor($type, $priority)
    {
        if ($priority === 'high') {
            return 'red';
        }

        return match($type) {
            Notification::TYPE_BOOKING_RECEIVED => 'blue',
            Notification::TYPE_BOOKING_COMPLETED => 'green',
            Notification::TYPE_BOOKING_CANCELLED => 'red',
            Notification::TYPE_BOOKING_CONFIRMED => 'amber',
            Notification::TYPE_SERVICE_UPDATED => 'purple',
            Notification::TYPE_PAYMENT_RECEIVED => 'green',
            Notification::TYPE_REVIEW_RECEIVED => 'yellow',
            Notification::TYPE_SYSTEM_ALERT => 'gray',
            default => 'gray'
        };
    }

    /**
     * Mark a specific notification as read
     */
    public function markNotificationAsRead($notificationId)
    {
        $vendor = auth()->user()->vendor;

        // Find the notification and ensure it belongs to the authenticated vendor
        $notification = Notification::forVendor($vendor->id)->findOrFail($notificationId);

        // Mark as read (using model method)
        $notification->markAsRead();

        return redirect()->back()->with('success', 'yey');
    }

    /**
     * Mark all notifications as read for the vendor
     */
    public function markAllNotificationsAsRead()
    {
        $vendor = auth()->user()->vendor;

        // Use the service method to mark all as read
        $updatedCount = $this->notificationService->markAllAsReadForVendor($vendor->id);

        return redirect()->back()->with('success', 'yey');
    }


    //EVENTS
    private function getUpcomingEvents($vendor)
    {

        return $vendor->bookings()
            ->select('bookings.*')
            ->join('events', 'bookings.event_id', '=', 'events.id')
            ->with(['service', 'user', 'event'])
            ->where('bookings.status', 'confirmed')
            ->where('events.event_date', '>=', now())
            ->orderBy('events.event_date', 'asc')
            ->take(3)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'event' => $this->formatEventTitle($booking),
                    'date' => $booking->event->event_date,
                    'time' => $booking->event->event_time ?? '12:00 PM',
                    'client' => $booking->user->name ?? 'Unknown Client',
                    'service' => $booking->service->name ?? 'Unknown Service',
                    'location' => $booking->event->location ?? null,
                    'status' => $booking->status
                ];
            });
    }

    private function getRecentBookings($vendor)
    {
        return $vendor->bookings()
            ->with(['service', 'user']) // Load relationships
            ->orderBy('created_at', 'desc') // Most recent first
            ->take(3) // Limit to 5 bookings
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'client' => $booking->user->name ?? 'Unknown Client',
                    'service' => $booking->service->name ?? 'Unknown Service',
                    'date' => $booking->created_at->format('Y-m-d'),
                    'event_date' => $booking->event_date ?? null,
                    'status' => $booking->status,
                    'amount' => $booking->service->price ?? 0,
                    'formatted_amount' => '₱' . number_format($booking->service->price ?? 0, 0)
                ];
            });
    }

    private function formatEventTitle($booking)
    {
        $service = $booking->service->name ?? 'Service';
        $client = $booking->user->name ?? 'Client';

        // You can customize this based on your needs
        return "{$service} for {$client}";
    }

    // Alternative method if you have a separate events table
    private function getUpcomingEventsFromEventsTable($vendor)
    {
        // If you have a separate events table related to bookings
        return $vendor->events() // Assuming you have an events relationship
            ->with(['booking.user', 'booking.service'])
            ->where('event_date', '>=', now())
            ->where('status', 'confirmed')
            ->orderBy('event_date', 'asc')
            ->take(5)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event' => $event->title ?? $this->formatEventTitle($event->booking),
                    'date' => $event->event_date,
                    'time' => $event->event_time,
                    'client' => $event->booking->user->name ?? 'Unknown Client',
                    'service' => $event->booking->service->name ?? 'Unknown Service',
                    'location' => $event->location,
                    'status' => $event->status
                ];
            });
    }

    // Enhanced version with more filtering options
    private function getUpcomingEventsEnhanced($vendor, $limit = 5, $days = 30)
    {
        return $vendor->bookings()
            ->with(['service', 'user'])
            ->whereIn('status', ['confirmed', 'pending']) // Include pending for visibility
            ->where('event_date', '>=', now())
            ->where('event_date', '<=', now()->addDays($days)) // Next 30 days
            ->orderBy('event_date', 'asc')
            ->orderBy('event_time', 'asc')
            ->take($limit)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'event' => $this->formatEventTitle($booking),
                    'date' => $booking->event_date,
                    'time' => $booking->event_time ?? '12:00 PM',
                    'client' => $booking->user->name ?? 'Unknown Client',
                    'service' => $booking->service->name ?? 'Unknown Service',
                    'location' => $booking->event_location ?? null,
                    'status' => $booking->status,
                    'priority' => $this->getEventPriority($booking),
                    'is_today' => $booking->event_date->isToday(),
                    'is_tomorrow' => $booking->event_date->isTomorrow(),
                    'days_until' => now()->diffInDays($booking->event_date)
                ];
            });
    }

    private function getEventPriority($booking)
    {
        $daysUntil = now()->diffInDays($booking->event_date);

        if ($daysUntil <= 1) return 'high';
        if ($daysUntil <= 7) return 'medium';
        return 'low';
    }

    // Method to get recent bookings with more details
    private function getRecentBookingsEnhanced($vendor, $limit = 5)
    {
        return $vendor->bookings()
            ->with(['service', 'user'])
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'client' => $booking->user->name ?? 'Unknown Client',
                    'client_email' => $booking->user->email ?? null,
                    'service' => $booking->service->name ?? 'Unknown Service',
                    'date' => $booking->created_at->format('Y-m-d'),
                    'event_date' => $booking->event_date ? $booking->event_date->format('Y-m-d') : null,
                    'status' => $booking->status,
                    'amount' => $booking->service->price ?? 0,
                    'formatted_amount' => '₱' . number_format($booking->service->price ?? 0, 0),
                    'is_new' => $booking->created_at->gt(now()->subDay()), // New if created within 24 hours
                    'time_ago' => $booking->created_at->diffForHumans(),
                    'status_color' => $this->getStatusColor($booking->status)
                ];
            });
    }

    private function getStatusColor($status)
    {
        return match($status) {
            'completed' => 'green',
            'confirmed' => 'amber',
            'pending' => 'gray',
            'cancelled' => 'red',
            default => 'gray'
        };
    }


    //Charts
    private function getChartData($vendor)
    {
        // Get the last 6 months including current month
        $months = collect();
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push($date);
            $labels[] = $date->format('M');
        }

        // Get bookings data per month
        $bookingsData = [];
        $revenueData = [];

        foreach ($months as $month) {
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();

            // Count bookings for this month
            $monthlyBookings = $vendor->bookings()
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            // Calculate revenue for completed bookings this month
            $monthlyRevenue = $vendor->bookings()
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->get()
                ->sum(function ($booking) {
                    return optional($booking->service)->price ?? 0;
                });

            $bookingsData[] = $monthlyBookings;
            $revenueData[] = (int) $monthlyRevenue; // Convert to integer for JSON
        }

        return [
            'revenue' => [
                'labels' => $labels,
                'revenue' => $revenueData
            ],
            'bookings' => [
                'labels' => $labels,
                'bookings' => $bookingsData
            ]
        ];
    }

    // Alternative method if you want weekly data instead of monthly
    private function getWeeklyChartData($vendor)
    {
        // Get the last 6 weeks including current week
        $weeks = collect();
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subWeeks($i);
            $weeks->push($date);
            $labels[] = 'Week ' . $date->weekOfYear;
        }

        $bookingsData = [];
        $revenueData = [];

        foreach ($weeks as $week) {
            $startOfWeek = $week->copy()->startOfWeek();
            $endOfWeek = $week->copy()->endOfWeek();

            // Count bookings for this week
            $weeklyBookings = $vendor->bookings()
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->count();

            // Calculate revenue for completed bookings this week
            $weeklyRevenue = $vendor->bookings()
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->get()
                ->sum(function ($booking) {
                    return optional($booking->service)->price ?? 0;
                });

            $bookingsData[] = $weeklyBookings;
            $revenueData[] = (int) $weeklyRevenue;
        }

        return [
            'revenue' => [
                'labels' => $labels,
                'revenue' => $revenueData
            ],
            'bookings' => [
                'labels' => $labels,
                'bookings' => $bookingsData
            ]
        ];
    }

    // More optimized version using database aggregation
    private function getOptimizedChartData($vendor)
    {
        // Get months
        $months = collect();
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push([
                'start' => $date->copy()->startOfMonth(),
                'end' => $date->copy()->endOfMonth(),
                'label' => $date->format('M')
            ]);
            $labels[] = $date->format('M');
        }

        $bookingsData = [];
        $revenueData = [];

        foreach ($months as $month) {
            // Get bookings count using database aggregation
            $monthlyBookings = $vendor->bookings()
                ->whereBetween('created_at', [$month['start'], $month['end']])
                ->count();

            // Get revenue using database join and aggregation for better performance
            $monthlyRevenue = $vendor->bookings()
                ->join('services', 'bookings.service_id', '=', 'services.id')
                ->where('bookings.status', 'completed')
                ->whereBetween('bookings.created_at', [$month['start'], $month['end']])
                ->sum('services.price');

            $bookingsData[] = $monthlyBookings;
            $revenueData[] = (int) ($monthlyRevenue ?? 0);
        }

        return [
            'revenue' => [
                'labels' => $labels,
                'revenue' => $revenueData
            ],
            'bookings' => [
                'labels' => $labels,
                'bookings' => $bookingsData
            ]
        ];
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('vendor');
            Vendor::create([
                'user_id' => $user->id,
                'business_name' => $request->business_name,
                'location' => $request->location,
                'contact_number' => $request->contact_number
            ]);

        return redirect()->back()->with('success', 'Vendor Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vendor $vendor)
    {
        return inertia('Admin/Users/Vendor/Show', compact('vendor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return redirect()->back()->with('success', 'Deleted Successfully');
    }
}
