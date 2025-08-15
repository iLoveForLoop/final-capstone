<?php

namespace App\Http\Controllers;


use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService){
        $this->notificationService = $notificationService;
    }

    public function getVendorUnreadBookingNotifications(){
        $vendor = auth()->user()->vendor;

        $unreadNotifications = $this->notificationService->getUnreadBookingCountForVendor($vendor->id);

        return response()->json([
                'success' => true,
                'data' => $unreadNotifications
            ]);
    }

}
