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

    // public function getVendorUnreadBookingNotifications(){
    //     $vendor = auth()->user()->vendor;

    //     $unreadNotifications = $this->notificationService->getUnreadBookingCountForVendor($vendor->id);

    //     return response()->json([
    //             'success' => true,
    //             'data' => $unreadNotifications
    //         ]);
    // }

    public function index(Request $request)
    {
        $vendorId = $request->user()->vendor->id;

        $notifications = Notification::forVendor($vendorId)
            ->latest()
            ->take(50) // limit to avoid overload
            ->get();

        return response()->json(['data' => $notifications]);
    }

    public function markAllRead(Request $request)
    {
        $vendorId = $request->user()->vendor->id;

        Notification::forVendor($vendorId)
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }


    public function markBookingRead(Request $request)
    {
        $vendorId = auth()->user()->vendor->id;

        Notification::forVendor($vendorId)
            ->unread()
            ->where('type', Notification::TYPE_BOOKING_RECEIVED)
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }




}