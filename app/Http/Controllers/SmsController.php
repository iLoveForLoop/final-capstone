<?php

namespace App\Http\Controllers;

use App\Services\SemaphoreService;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function sendTest()
    {
        $sms = new SemaphoreService();
        $result = $sms->send('639939852649', 'Your Booking has been confirmed');

        return response()->json($result);
    }
}
