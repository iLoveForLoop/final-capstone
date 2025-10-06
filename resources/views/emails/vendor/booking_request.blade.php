<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking Request - Eventory</title>
</head>

<body
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #f8fafc; color: #334155; margin:0; padding:0; line-height: 1.6;">
    <!-- Preheader Text -->
    <div style="display: none; max-height: 0; overflow: hidden;">
        New booking request for {{ $booking->service->name }} from {{ $booking->user->name }}
    </div>

    <div
        style="max-width:600px; margin:0 auto; background:#fff; border-radius:8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
        <!-- Header -->
        <table width="100%" cellpadding="0" cellspacing="0"
            style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
            <tr>
                <td style="padding: 30px 20px; text-align: center;">
                    <h1
                        style="color: #ffffff; font-size: 28px; font-weight: 600; margin: 0 0 8px 0; letter-spacing: -0.5px;">
                        Eventory</h1>
                </td>
            </tr>
        </table>

        <!-- Status Badge -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background: #fef3c7;">
            <tr>
                <td style="padding: 20px; text-align: center;">
                    <div
                        style="display: inline-block; background: #d97706; color: #ffffff; padding: 12px 24px; border-radius: 24px; font-weight: 600; font-size: 14px;">
                        New Booking Request
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
                        Great news! You've received a new booking request through Eventory.
                        A client has selected your service and is awaiting your confirmation.
                    </p>

                    <!-- Booking Card -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin: 0 0 32px 0;">
                        <tr>
                            <td style="padding: 24px;">
                                <h2
                                    style="color: #1e293b; font-size: 18px; font-weight: 600; margin: 0 0 20px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
                                    Booking Request Details</h2>

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
                                            Status:</td>
                                        <td style="padding: 8px 0;">
                                            <span
                                                style="background: #dbeafe; color: #1e40af; padding: 6px 14px; border-radius: 16px; font-size: 12px; font-weight: 600; display: inline-block;">Pending
                                                Confirmation</span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- Action Required -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; margin: 0 0 32px 0;">
                        <tr>
                            <td style="padding: 20px;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td width="40" style="vertical-align: top;">
                                            <div
                                                style="background: #d97706; color: #ffffff; width: 24px; height: 24px; border-radius: 50%; text-align: center; line-height: 24px; font-size: 14px; font-weight: bold;">
                                                !</div>
                                        </td>
                                        <td style="padding-left: 12px;">
                                            <p
                                                style="color: #92400e; font-size: 14px; font-weight: 600; margin: 0 0 4px 0;">
                                                Action Required</p>
                                            <p style="color: #78350f; font-size: 13px; margin: 0; line-height: 1.5;">
                                                Please review this booking request and respond within 24 hours to
                                                maintain your vendor response rating and provide excellent customer
                                                service.
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
                                <a href="{{ route('vendor.bookings.show', $booking->id) }}"
                                    style="display: inline-block; padding: 14px 36px; background: #4f46e5; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 15px; transition: all 0.2s;">
                                    Review Booking Request
                                </a>
                            </td>
                        </tr>
                    </table>

                    <!-- Next Steps -->
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 24px 0;">
                        <tr>
                            <td>
                                <h3 style="color: #1e293b; font-size: 16px; font-weight: 600; margin: 0 0 12px 0;">Next
                                    Steps</h3>
                                <ul
                                    style="color: #475569; font-size: 14px; margin: 0; padding-left: 20px; line-height: 1.6;">
                                    <li style="margin-bottom: 8px;">Review the booking details and client requirements
                                    </li>
                                    <li style="margin-bottom: 8px;">Confirm your availability for the requested date
                                    </li>
                                    <li style="margin-bottom: 8px;">Communicate any questions directly with the client
                                    </li>
                                    <li>Update the booking status once confirmed or declined</li>
                                </ul>
                            </td>
                        </tr>
                    </table>

                    <!-- Client Contact -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background: #f8fafc; border-radius: 8px; padding: 20px; margin: 0 0 24px 0;">
                        <tr>
                            <td>
                                <h4 style="color: #1e293b; font-size: 15px; font-weight: 600; margin: 0 0 12px 0;">
                                    Client Information</h4>
                                <p style="color: #475569; font-size: 14px; margin: 0 0 8px 0;">
                                    <strong>{{ $booking->user->name }}</strong><br>
                                    Email: {{ $booking->user->email }}
                                    Contact Number: {{ $booking->user->client->contact_number }}
                                </p>
                            </td>
                        </tr>
                    </table>

                    <!-- Closing -->
                    <p style="font-size: 15px; color: #475569; margin: 0 0 8px 0;">We appreciate your partnership with
                        Eventory.</p>
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
                        Our vendor support team is available to help with any questions about this booking or your
                        vendor account.
                    </p>
                    <a href="mailto:support@eventory.com"
                        style="color: #4f46e5; text-decoration: none; font-size: 13px; font-weight: 500;">Contact Vendor
                        Support →</a>
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
