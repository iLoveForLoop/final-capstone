<?php

namespace App\Http\Controllers;

use App\Mail\EmailOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailOtp;
use App\Models\User;
use Carbon\Carbon;

class EmailOtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $emailExist = User::where('email', $request->email)->exists();
        if($emailExist){
            return response()->json([
                'message' => 'This email is already registered. Please use a different email.'
            ], 409);
        }

        // Find a non-expired OTP for this email
        $existingOtp = EmailOtp::where('email', $request->email)
            ->where('expires_at', '>', now())
            ->first();

        // Rate limit: prevent sending OTP too frequently (every 30 seconds)
        if ($existingOtp && $existingOtp->updated_at->diffInSeconds(now()) < 30) {
            return response()->json(['message' => 'Please wait before requesting another OTP.'], 429);
        }

        $otp = rand(100000, 999999);

        EmailOtp::updateOrCreate(
            ['email' => $request->email],
            [
                'otp_code' => $otp,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]
        );

        Mail::to($request->email)->send(new EmailOtpMail($otp));

        return response()->json(['message' => 'OTP sent successfully!']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|numeric',
        ]);

        $record = EmailOtp::where('email', $request->email)->first();

        if (!$record) {
            return response()->json(['message' => 'OTP not found.'], 404);
        }

        if ($record->otp_code !== $request->otp_code) {
            return response()->json(['message' => 'Invalid OTP.'], 400);
        }

        if (Carbon::now()->greaterThan($record->expires_at)) {
            return response()->json(['message' => 'OTP expired.'], 400);
        }

        // Delete OTP to prevent reuse
        $record->delete();

        // Mark as verified (frontend will handle proceeding to register)
        return response()->json(['message' => 'OTP verified successfully!']);
    }
}
