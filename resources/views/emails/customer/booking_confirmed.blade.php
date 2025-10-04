<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - Eventory</title>
</head>

<body
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #f8fafc; color: #334155; margin:0; padding:0; line-height: 1.6;">
    <!-- Preheader Text -->
    <div style="display: none; max-height: 0; overflow: hidden;">
        Your booking for {{ $booking->service->name }} has been confirmed by
        {{ $booking->service->vendor->business_name }}
    </div>

    <div
        style="max-width:600px; margin:0 auto; background:#fff; border-radius:8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
        <!-- Header -->
        <table width="100%" cellpadding="0" cellspacing="0"
            style="background: linear-gradient(135deg, #10b981, #34d399);">
            <tr>
                <td style="padding: 30px 20px; text-align: center;">
                    <h1
                        style="color: #ffffff; font-size: 28px; font-weight: 600; margin: 0 0 8px 0; letter-spacing: -0.5px;">
                        Eventory</h1>
                </td>
            </tr>
        </table>

        <!-- Confirmation Badge -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background: #ecfdf5;">
            <tr>
                <td style="padding: 20px; text-align: center;">
                    <div
                        style="display: inline-block; background: #10b981; color: #ffffff; padding: 12px 24px; border-radius: 24px; font-weight: 600; font-size: 14px;">
                        Booking Confirmed
                    </div>
                </td>
            </tr>
        </table>

        <!-- Main Content -->
        <table width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 30px;">
            <tr>
                <td>
                    <!-- Greeting -->
                    <p style="font-size: 16px; color: #475569; margin: 0 0 24px 0;">Dear {{ $booking->user->name }},</p>

                    <!-- Main Message -->
                    <p style="font-size: 15px; color: #475569; margin: 0 0 32px 0; line-height: 1.7;">
                        Great news! Your booking request has been confirmed by
                        <strong>{{ $booking->service->vendor->business_name }}</strong>.
                        Your event is now officially scheduled and the vendor is looking forward to making your occasion
                        memorable.
                    </p>

                    <!-- Booking Card -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin: 0 0 32px 0;">
                        <tr>
                            <td style="padding: 24px;">
                                <h2
                                    style="color: #1e293b; font-size: 18px; font-weight: 600; margin: 0 0 20px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
                                    Booking Confirmation</h2>

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
                                            Vendor:</td>
                                        <td style="padding: 8px 0; color: #1e293b; font-size: 14px;">
                                            {{ $booking->service->vendor->business_name }}</td>
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
                                            Status:</td>
                                        <td style="padding: 8px 0;">
                                            <span
                                                style="background: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 16px; font-size: 12px; font-weight: 600; display: inline-block;">Confirmed</span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- Success Message -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; margin: 0 0 32px 0;">
                        <tr>
                            <td style="padding: 20px;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td width="40" style="vertical-align: top;">
                                            <div
                                                style="background: #0ea5e9; color: #ffffff; width: 24px; height: 24px; border-radius: 50%; text-align: center; line-height: 24px; font-size: 14px; font-weight: bold;">
                                                ✓</div>
                                        </td>
                                        <td style="padding-left: 12px;">
                                            <p
                                                style="color: #0369a1; font-size: 14px; font-weight: 600; margin: 0 0 4px 0;">
                                                Your Event is Locked In!</p>
                                            <p style="color: #0c4a6e; font-size: 13px; margin: 0; line-height: 1.5;">
                                                The vendor has confirmed their availability and is committed to
                                                delivering exceptional service for your event.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- CTA Button -->
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 32px 0;">
                        <tr>
                            <td align="center">
                                <a href="{{ route('client.bookings.index') }}"
                                    style="display: inline-block; padding: 14px 36px; background: #10b981; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 15px; transition: all 0.2s;">
                                    View Booking Details
                                </a>
                            </td>
                        </tr>
                    </table>

                    <!-- Next Steps -->
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 24px 0;">
                        <tr>
                            <td>
                                <h3 style="color: #1e293b; font-size: 16px; font-weight: 600; margin: 0 0 12px 0;">What
                                    Happens Next?</h3>
                                <ul
                                    style="color: #475569; font-size: 14px; margin: 0; padding-left: 20px; line-height: 1.6;">
                                    <li style="margin-bottom: 8px;">The vendor may contact you to discuss specific
                                        details and requirements</li>
                                    <li style="margin-bottom: 8px;">You'll receive reminders as your event date
                                        approaches</li>
                                    <li style="margin-bottom: 8px;">Any updates or changes can be managed through your
                                        Eventory dashboard</li>
                                    <li>After your event, you'll have the opportunity to share your experience</li>
                                </ul>
                            </td>
                        </tr>
                    </table>

                    <!-- Vendor Contact -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background: #f8fafc; border-radius: 8px; padding: 20px; margin: 0 0 24px 0;">
                        <tr>
                            <td>
                                <h4 style="color: #1e293b; font-size: 15px; font-weight: 600; margin: 0 0 12px 0;">
                                    Vendor Information</h4>
                                <p style="color: #475569; font-size: 14px; margin: 0 0 8px 0;">
                                    <strong>{{ $booking->service->vendor->business_name }}</strong><br>
                                    Phone: {{ $booking->service->vendor->contact_number }}<br>
                                    Email: {{ $booking->service->vendor->user->email }}
                                </p>
                            </td>
                        </tr>
                    </table>

                    <!-- Closing -->
                    <p style="font-size: 15px; color: #475569; margin: 0 0 8px 0;">We're excited to be part of your
                        special occasion!</p>
                    <p style="font-size: 15px; color: #475569; margin: 0;">Best regards,<br>The Eventory Team</p>
                </td>
            </tr>
        </table>

        <!-- Support Section -->
        <table width="100%" cellpadding="0" cellspacing="0"
            style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
            <tr>
                <td style="padding: 20px 30px;">
                    <p style="color: #64748b; font-size: 13px; margin: 0 0 8px 0; font-weight: 500;">Need Assistance?
                    </p>
                    <p style="color: #475569; font-size: 13px; margin: 0 0 12px 0;">
                        Our customer support team is here to help with any questions about your booking or the Eventory
                        platform.
                    </p>
                    <a href="mailto:support@eventory.com"
                        style="color: #10b981; text-decoration: none; font-size: 13px; font-weight: 500;">Contact
                        Customer Support →</a>
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
                        <a href="#" style="color: #94a3b8; text-decoration: none; margin: 0 12px;">Customer
                            Portal</a>
                    </p>
                    <p style="font-size: 11px; color: #64748b; margin: 0;">
                        This email was sent to you as a registered customer of Eventory.
                        <a href="#" style="color: #64748b; text-decoration: underline;">Unsubscribe</a> from
                        booking notifications.
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
