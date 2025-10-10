<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService){
        $this->notificationService = $notificationService;
    }

    // Unified index method for both vendor and client
    public function index(Request $request)
    {
        $user = $request->user();

        $limit = $request->get('limit', 50);

        if ($user->hasRole('vendor') && $user->vendor) {
            $notifications = Notification::forVendor($user->vendor->id)
                ->latest()
                ->take($limit)
                ->get();
        } elseif ($user->hasRole('client')) {
            $notifications = Notification::forUser($user->client->id)
                ->latest()
                ->take($limit)
                ->get();
        } else {
            return response()->json(['data' => []]);
        }

        return response()->json(['data' => $notifications]);
    }

    // Unified mark all as read
    public function markAllRead(Request $request)
    {
        $user = $request->user();


        if ($user->hasRole('vendor') && $user->vendor) {
            Notification::forVendor($user->vendor->id)
                ->unread()
                ->update(['read_at' => now()]);
        } elseif ($user->hasRole('client')) {
            Notification::forUser($user->client->id)
                ->unread()
                ->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    // Mark specific notification as read
    public function markAsRead(Request $request, Notification $notification)
    {
        $user = $request->user();

        // Check if notification belongs to the user
        if ($user->hasRole('vendor') && $user->vendor && $notification->vendor_id === $user->vendor->id) {
            $notification->markAsRead();
        } elseif ($user->hasRole('client') && $notification->client_id === $user->client->id) {
            $notification->markAsRead();
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json(['success' => true]);
    }

    // Vendor-specific methods (keep for backward compatibility)
    public function markBookingRead(Request $request)
    {
        $vendorId = $request->user()->vendor->id;

        Notification::forVendor($vendorId)
            ->unread()
            ->where('type', Notification::TYPE_BOOKING_RECEIVED)
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    // Get unread counts for both vendor and client
    public function getUnreadCounts(Request $request)
    {
        $user = $request->user();
        $counts = [];

        if ($user->vendor) {
            $counts['vendor'] = [
                'total' => Notification::forVendor($user->vendor->id)->unread()->count(),
                'booking' => Notification::forVendor($user->vendor->id)
                    ->unread()
                    ->where('type', Notification::TYPE_BOOKING_RECEIVED)
                    ->count()
            ];
        }

        $counts['client'] = [
            'total' => Notification::forUser($user->id)->unread()->count()
        ];

        return response()->json(['data' => $counts]);
    }

    // Get client unread count
    public function getUnreadCount(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('client')) {
            $unreadCount = Notification::forUser($user->id)->unread()->count();
            return response()->json(['unread_count' => $unreadCount]);
        }

        return response()->json(['unread_count' => 0]);
    }
}