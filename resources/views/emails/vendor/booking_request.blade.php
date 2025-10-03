<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>New Booking Request</title>
</head>

<body style="font-family: Arial, sans-serif; background: #f9f9f9; color: #333; margin:0; padding:0;">
    <div style="max-width:600px; margin:0 auto; background:#fff; padding:20px; border-radius:8px;">

        <div
            style="background:#4f46e5; color:#fff; padding:15px; text-align:center; font-size:20px; font-weight:bold; border-radius:8px 8px 0 0;">
            Eventory
        </div>

        <p>Hello {!! $booking->service->vendor->business_name !!},</p>

        <p>You’ve received a new booking for <strong>{{ $booking->service->name }}</strong>
            from {{ $booking->user->name }}.</p>

        <p style="text-align:center;">
            <a href="{{ route('vendor.bookings.index') }}"
                style="display:inline-block; padding:10px 15px; background:#4f46e5; color:#fff; text-decoration:none; border-radius:4px; margin-top:20px;">
                View Booking
            </a>
        </p>

        <div style="text-align:center; font-size:12px; color:#888; margin-top:20px;">
            © {{ date('Y') }} Eventory. All rights reserved.
        </div>

    </div>
</body>

</html>
