<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Completed - Eventory</title>
</head>

<body
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #f8fafc; color: #334155; margin:0; padding:0; line-height: 1.6;">
    <!-- Preheader Text -->
    <div style="display: none; max-height: 0; overflow: hidden;">
        Your {{ $booking->service->name }} service has been successfully completed by
        {{ $booking->service->vendor->business_name }}
    </div>

    <div
        style="max-width:600px; margin:0 auto; background:#fff; border-radius:8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
        <!-- Header -->
        <table width="100%" cellpadding="0" align="center" cellspacing="0"
            style="background: linear-gradient(135deg, #8b5cf6, #a78bfa);">
            <tr>
                <td align="center" valign="middle" style="padding: 30px 20px; text-align: center;">
                    <div
                        style="color:#ffffff; font-size:28px; font-weight:600; margin:0; letter-spacing:-0.5px; font-family:Helvetica, Arial, sans-serif; text-align: center;">
                        Eventory
                    </div>
                </td>
            </tr>
        </table>

        <!-- Completion Badge -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background: #faf5ff;">
            <tr>
                <td style="padding: 20px; text-align: center;">
                    <div
                        style="display: inline-block; background: #8b5cf6; color: #ffffff; padding: 12px 24px; border-radius: 24px; font-weight: 600; font-size: 14px;">
                        Service Completed Successfully
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
                        We're delighted to inform you that your <strong>{{ $booking->service->name }}</strong> service
                        with <strong>{{ $booking->service->vendor->business_name }}</strong> has been successfully
                        completed!
                        We hope your event was everything you envisioned and more.
                    </p>

                    <!-- Booking Card -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin: 0 0 32px 0;">
                        <tr>
                            <td style="padding: 24px;">
                                <h2
                                    style="color: #1e293b; font-size: 18px; font-weight: 600; margin: 0 0 20px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
                                    Service Completion Details</h2>

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
                                            Completed On:</td>
                                        <td style="padding: 8px 0; color: #1e293b; font-size: 14px;">
                                            {{ $booking->updated_at->format('F j, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td width="120"
                                            style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                            Status:</td>
                                        <td style="padding: 8px 0;">
                                            <span
                                                style="background: #ede9fe; color: #6d28d9; padding: 6px 14px; border-radius: 16px; font-size: 12px; font-weight: 600; display: inline-block;">Completed</span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- Celebration Message -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; margin: 0 0 32px 0;">
                        <tr>
                            <td style="padding: 20px;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td width="40" style="vertical-align: top;">
                                            <div
                                                style="background: #0ea5e9; color: #ffffff; width: 24px; height: 24px; border-radius: 50%; text-align: center; line-height: 24px; font-size: 14px; font-weight: bold;">
                                                ⭐</div>
                                        </td>
                                        <td style="padding-left: 12px;">
                                            <p
                                                style="color: #0369a1; font-size: 14px; font-weight: 600; margin: 0 0 4px 0;">
                                                Thank You for Choosing Eventory!</p>
                                            <p style="color: #0c4a6e; font-size: 13px; margin: 0; line-height: 1.5;">
                                                We hope you had a wonderful experience. Your feedback helps us maintain
                                                quality
                                                standards and assists other customers in making informed decisions.
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
                                <a href="{{ route('client.bookings.index', $booking->id) }}"
                                    style="display: inline-block; padding: 14px 36px; background: #8b5cf6; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 15px; transition: all 0.2s;">
                                    Leave a Review
                                </a>
                            </td>
                        </tr>
                    </table>

                    <!-- Next Steps -->
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 24px 0;">
                        <tr>
                            <td>
                                <h3 style="color: #1e293b; font-size: 16px; font-weight: 600; margin: 0 0 12px 0;">Share
                                    Your Experience</h3>
                                <ul
                                    style="color: #475569; font-size: 14px; margin: 0; padding-left: 20px; line-height: 1.6;">
                                    <li style="margin-bottom: 8px;">Help other customers by sharing your experience with
                                        this vendor</li>
                                    <li style="margin-bottom: 8px;">Your review helps vendors improve their services
                                    </li>
                                    <li style="margin-bottom: 8px;">Rate your overall experience from 1 to 5 stars</li>
                                    <li>Share photos from your event (optional)</li>
                                </ul>
                            </td>
                        </tr>
                    </table>

                    <!-- Vendor Appreciation -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background: #f8fafc; border-radius: 8px; padding: 20px; margin: 0 0 24px 0;">
                        <tr>
                            <td>
                                <h4 style="color: #1e293b; font-size: 15px; font-weight: 600; margin: 0 0 12px 0;">
                                    Vendor Information</h4>
                                <p style="color: #475569; font-size: 14px; margin: 0 0 8px 0;">
                                    <strong>{{ $booking->service->vendor->business_name }}</strong><br>
                                    @if ($booking->service->vendor->contact_number)
                                        Phone: {{ $booking->service->vendor->contact_number }}<br>
                                    @endif
                                    Email: {{ $booking->service->vendor->user->email }}
                                </p>
                                <p style="color: #64748b; font-size: 13px; margin: 8px 0 0 0;">
                                    Consider keeping this vendor's contact information for future events!
                                </p>
                            </td>
                        </tr>
                    </table>

                    <!-- Future Bookings -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 20px; margin: 0 0 24px 0;">
                        <tr>
                            <td>
                                <h4 style="color: #065f46; font-size: 15px; font-weight: 600; margin: 0 0 8px 0;">
                                    Planning Another Event?</h4>
                                <p style="color: #047857; font-size: 14px; margin: 0;">
                                    Browse our extensive network of vendors for your next celebration. From weddings to
                                    corporate events,
                                    we have the perfect services to make your next occasion unforgettable.
                                </p>
                            </td>
                        </tr>
                    </table>

                    <!-- Closing -->
                    <p style="font-size: 15px; color: #475569; margin: 0 0 8px 0;">Thank you for trusting Eventory with
                        your special occasion!</p>
                    <p style="font-size: 15px; color: #475569; margin: 0;">Warm regards,<br>The Eventory Team</p>
                </td>
            </tr>
        </table>

        <!-- Support Section -->
        <table width="100%" cellpadding="0" cellspacing="0"
            style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
            <tr>
                <td style="padding: 20px 30px;">
                    <p style="color: #64748b; font-size: 13px; margin: 0 0 8px 0; font-weight: 500;">Need Assistance
                        with Your Completed Booking?</p>
                    <p style="color: #475569; font-size: 13px; margin: 0 0 12px 0;">
                        Our customer support team is available if you have any questions about your completed service or
                        need assistance with future bookings.
                    </p>
                    <a href="mailto:support@eventory.com"
                        style="color: #8b5cf6; text-decoration: none; font-size: 13px; font-weight: 500;">Contact
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
