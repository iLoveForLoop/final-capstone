<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Cancelled by Customer - Eventory</title>
</head>

<body
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #f8fafc; color: #334155; margin:0; padding:0; line-height: 1.6;">
    <!-- Preheader Text -->
    <div style="display: none; max-height: 0; overflow: hidden;">
        Booking #{{ $booking->id }} for {{ $booking->service->name }} has been cancelled by {{ $booking->user->name }}
    </div>

    <div
        style="max-width:600px; margin:0 auto; background:#fff; border-radius:8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
        <!-- Header -->
        <table width="100%" cellpadding="0" cellspacing="0"
            style="background: linear-gradient(135deg, #ef4444, #f87171);">
            <tr>
                <td style="padding: 30px 20px; text-align: center;">
                    <h1
                        style="color: #ffffff; font-size: 28px; font-weight: 600; margin: 0 0 8px 0; letter-spacing: -0.5px;">
                        Eventory</h1>
                </td>
            </tr>
        </table>

        <!-- Cancellation Badge -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background: #fef2f2;">
            <tr>
                <td style="padding: 20px; text-align: center;">
                    <div
                        style="display: inline-block; background: #ef4444; color: #ffffff; padding: 12px 24px; border-radius: 24px; font-weight: 600; font-size: 14px;">
                        Booking Cancelled by Customer
                    </div>
                </td>
            </tr>
        </table>

        <!-- Main Content -->
        <table width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 30px;">
            <tr>
                <td>
                    <!-- Greeting -->
                    <p style="font-size: 16px; color: #475569; margin: 0 0 24px 0;">Dear
                        {{ $booking->service->vendor->business_name }},</p>

                    <!-- Main Message -->
                    <p style="font-size: 15px; color: #475569; margin: 0 0 32px 0; line-height: 1.7;">
                        We're writing to inform you that <strong>{{ $booking->user->name }}</strong> has cancelled their
                        booking
                        for your <strong>{{ $booking->service->name }}</strong> service. While cancellations are
                        sometimes unavoidable,
                        we appreciate your understanding.
                    </p>

                    <!-- Booking Card -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin: 0 0 32px 0;">
                        <tr>
                            <td style="padding: 24px;">
                                <h2
                                    style="color: #1e293b; font-size: 18px; font-weight: 600; margin: 0 0 20px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
                                    Cancelled Booking Details</h2>

                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td width="120"
                                            style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                            Service:</td>
                                        <td style="padding: 8px 0; color: #1e293b; font-size: 14px; font-weight: 600;">
                                            {{ $booking->service->name }}</td>
                                    </tr>
                                    <tr>
                                        <td width="120"
                                            style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                            Client:</td>
                                        <td style="padding: 8px 0; color: #1e293b; font-size: 14px;">
                                            {{ $booking->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td width="120"
                                            style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                            Booking ID:</td>
                                        <td
                                            style="padding: 8px 0; color: #1e293b; font-size: 14px; font-family: monospace;">
                                            #{{ $booking->id }}</td>
                                    </tr>
                                    <tr>
                                        <td width="120"
                                            style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                            Event Date:</td>
                                        <td style="padding: 8px 0; color: #1e293b; font-size: 14px;">
                                            {{ $booking->booking_date->format('F j, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td width="120"
                                            style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                            Cancelled On:</td>
                                        <td style="padding: 8px 0; color: #1e293b; font-size: 14px;">
                                            {{ $booking->updated_at->format('F j, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td width="120"
                                            style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                            Status:</td>
                                        <td style="padding: 8px 0;">
                                            <span
                                                style="background: #fecaca; color: #dc2626; padding: 6px 14px; border-radius: 16px; font-size: 12px; font-weight: 600; display: inline-block;">Cancelled
                                                by Customer</span>
                                        </td>
                                    </tr>
                                    @if ($cancellationReason)
                                        <tr>
                                            <td width="120"
                                                style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                                Reason:</td>
                                            <td style="padding: 8px 0; color: #1e293b; font-size: 14px;">
                                                {{ $cancellationReason }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($additionalComment)
                                        <tr>
                                            <td width="120"
                                                style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                                Comment:</td>
                                            <td style="padding: 8px 0; color: #1e293b; font-size: 14px;">
                                                {{ $additionalComment }}
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- Impact Information -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; margin: 0 0 32px 0;">
                        <tr>
                            <td style="padding: 20px;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td width="40" style="vertical-align: top;">
                                            <div
                                                style="background: #0ea5e9; color: #ffffff; width: 24px; height: 24px; border-radius: 50%; text-align: center; line-height: 24px; font-size: 14px; font-weight: bold;">
                                                ℹ️</div>
                                        </td>
                                        <td style="padding-left: 12px;">
                                            <p
                                                style="color: #0369a1; font-size: 14px; font-weight: 600; margin: 0 0 4px 0;">
                                                Impact on Your Schedule</p>
                                            <p style="color: #0c4a6e; font-size: 13px; margin: 0; line-height: 1.5;">
                                                This cancellation has automatically freed up your availability for
                                                {{ $booking->booking_date->format('F j, Y') }}. Your calendar has been
                                                updated accordingly.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- CTA Buttons -->
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 32px 0;">
                        <tr>
                            <td align="center">
                                <a href="{{ route('vendor.bookings.index') }}"
                                    style="display: inline-block; padding: 12px 24px; background: #ef4444; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; margin: 0 8px; transition: all 0.2s;">
                                    View Cancelled Booking
                                </a>
                                {{-- <a href="{{ route('vendor.dashboard') }}"
                                    style="display: inline-block; padding: 12px 24px; background: #4f46e5; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; margin: 0 8px; transition: all 0.2s;">
                                    Manage Availability
                                </a> --}}
                            </td>
                        </tr>
                    </table>

                    <!-- Next Steps -->
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 24px 0;">
                        <tr>
                            <td>
                                <h3 style="color: #1e293b; font-size: 16px; font-weight: 600; margin: 0 0 12px 0;">What
                                    This Means For You</h3>
                                <ul
                                    style="color: #475569; font-size: 14px; margin: 0; padding-left: 20px; line-height: 1.6;">
                                    <li style="margin-bottom: 8px;">Your availability for this date has been
                                        automatically restored</li>
                                    <li style="margin-bottom: 8px;">No further action is required from your side</li>
                                    <li style="margin-bottom: 8px;">Your vendor rating is not affected by customer
                                        cancellations</li>
                                    <li>You can now accept new bookings for this date</li>
                                </ul>
                            </td>
                        </tr>
                    </table>

                    <!-- Positive Outlook -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 20px; margin: 0 0 24px 0;">
                        <tr>
                            <td>
                                <h4 style="color: #065f46; font-size: 15px; font-weight: 600; margin: 0 0 8px 0;">
                                    Looking Ahead</h4>
                                <p style="color: #047857; font-size: 14px; margin: 0;">
                                    While cancellations happen, this opens up new opportunities. Your newly available
                                    slot
                                    can now be booked by another client looking for your excellent services.
                                </p>
                            </td>
                        </tr>
                    </table>

                    <!-- Closing -->
                    <p style="font-size: 15px; color: #475569; margin: 0 0 8px 0;">Thank you for your understanding and
                        continued partnership with Eventory.</p>
                    <p style="font-size: 15px; color: #475569; margin: 0;">Best regards,<br>The Eventory Team</p>
                </td>
            </tr>
        </table>

        <!-- Support Section -->
        <table width="100%" cellpadding="0" cellspacing="0"
            style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
            <tr>
                <td style="padding: 20px 30px;">
                    <p style="color: #64748b; font-size: 13px; margin: 0 0 8px 0; font-weight: 500;">Questions About
                        This Cancellation?</p>
                    <p style="color: #475569; font-size: 13px; margin: 0 0 12px 0;">
                        Our vendor support team is here to help if you have any questions about this cancellation or
                        need assistance managing your bookings.
                    </p>
                    <a href="mailto:support@eventory.com"
                        style="color: #ef4444; text-decoration: none; font-size: 13px; font-weight: 500;">Contact
                        Vendor Support →</a>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background: #1e293b; color: #cbd5e1;">
            <tr>
                <td style="padding: 24px 30px; text-align: center;">
                    <p style="font-size: 12px; margin: 0 0 12px 0;">
                        © {{ date('Y') }} Eventory Inc. • 123 Business Avenue, Suite 100 • New York, NY 10001
                    </p>
                    <p style="font-size: 12px; margin: 0 0 16px 0;">
                        <a href="#" style="color: #94a3b8; text-decoration: none; margin: 0 12px;">Privacy
                            Policy</a>
                        <a href="#" style="color: #94a3b8; text-decoration: none; margin: 0 12px;">Terms of
                            Service</a>
                        <a href="#" style="color: #94a3b8; text-decoration: none; margin: 0 12px;">Vendor
                            Portal</a>
                    </p>
                    <p style="font-size: 11px; color: #64748b; margin: 0;">
                        This email was sent to you as a registered vendor of Eventory.
                        <a href="#" style="color: #64748b; text-decoration: underline;">Unsubscribe</a> from
                        booking notifications.
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
