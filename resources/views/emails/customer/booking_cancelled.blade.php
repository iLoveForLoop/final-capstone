<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Cancelled - Eventory</title>
</head>

<body
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #f8fafc; color: #334155; margin:0; padding:0; line-height: 1.6;">
    <!-- Preheader Text -->
    <div style="display: none; max-height: 0; overflow: hidden;">
        Your booking for {{ $booking->service->name }} has been cancelled by
        {{ $booking->service->vendor->business_name }}
    </div>

    <div
        style="max-width:600px; margin:0 auto; background:#fff; border-radius:8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
        <!-- Header -->
        <table width="100%" cellpadding="0" cellspacing="0"
            style="background: linear-gradient(135deg, #ef4444, #f87171);">
            <tr>
                <td align="center" valign="middle" style="padding: 30px 20px; text-align: center;">
                    <h1
                        style="color: #ffffff; font-size: 28px; font-weight: 600; margin: 0; letter-spacing: -0.5px; text-align: center;">
                        Eventory
                    </h1>
                </td>
            </tr>
        </table>

        <!-- Cancellation Badge -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background: #fef2f2;">
            <tr>
                <td style="padding: 20px; text-align: center;">
                    <div
                        style="display: inline-block; background: #ef4444; color: #ffffff; padding: 12px 24px; border-radius: 24px; font-weight: 600; font-size: 14px;">
                        Booking Cancelled
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
                        We regret to inform you that your booking with
                        <strong>{{ $booking->service->vendor->business_name }}</strong>
                        has been cancelled. We understand this may be disappointing and sincerely apologize for any
                        inconvenience this may cause.
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
                                                style="background: #fecaca; color: #dc2626; padding: 6px 14px; border-radius: 16px; font-size: 12px; font-weight: 600; display: inline-block;">Cancelled</span>
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
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- Refund Information -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; margin: 0 0 32px 0;">
                        <tr>
                            <td style="padding: 20px;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding-left: 12px;">
                                            <p
                                                style="color: #0369a1; font-size: 14px; font-weight: 600; margin: 0 0 4px 0;">
                                                Refund Process</p>
                                            <p style="color: #0c4a6e; font-size: 13px; margin: 0; line-height: 1.5;">
                                                @if ($booking->amount_paid > 0)
                                                    A refund of
                                                    <strong>${{ number_format($booking->amount_paid, 2) }}</strong> will
                                                    be processed
                                                    to your original payment method within 5-7 business days.
                                                @else
                                                    No payment was processed for this booking, so no refund is
                                                    necessary.
                                                @endif
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
                                <a href="{{ route('client.bookings.index') }}"
                                    style="display: inline-block; padding: 12px 24px; background: #ef4444; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; margin: 0 8px; transition: all 0.2s;">
                                    View Cancelled Booking
                                </a>
                                <a href="{{ route('client.service.index') }}"
                                    style="display: inline-block; padding: 12px 24px; background: #4f46e5; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; margin: 0 8px; transition: all 0.2s;">
                                    Find Another Vendor
                                </a>
                            </td>
                        </tr>
                    </table>

                    <!-- Next Steps -->
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 24px 0;">
                        <tr>
                            <td>
                                <h3 style="color: #1e293b; font-size: 16px; font-weight: 600; margin: 0 0 12px 0;">What
                                    You Can Do Next</h3>
                                <ul
                                    style="color: #475569; font-size: 14px; margin: 0; padding-left: 20px; line-height: 1.6;">
                                    <li style="margin-bottom: 8px;">Browse similar services available for your event
                                        date</li>
                                    <li style="margin-bottom: 8px;">Contact our support team if you need assistance
                                        finding a replacement</li>
                                    <li style="margin-bottom: 8px;">Review your refund status in your account dashboard
                                    </li>
                                    <li>Contact us if you have any questions about the cancellation</li>
                                </ul>
                            </td>
                        </tr>
                    </table>

                    <!-- Alternative Options -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background: #f8fafc; border-radius: 8px; padding: 20px; margin: 0 0 24px 0;">
                        <tr>
                            <td>
                                <h4 style="color: #1e293b; font-size: 15px; font-weight: 600; margin: 0 0 12px 0;">
                                    Need Help Finding a Replacement?</h4>
                                <p style="color: #475569; font-size: 14px; margin: 0 0 8px 0;">
                                    Our team can help you find alternative vendors for your event. We'll prioritize
                                    vendors with availability on your preferred date and similar service offerings.
                                </p>
                            </td>
                        </tr>
                    </table>

                    <!-- Closing -->
                    <p style="font-size: 15px; color: #475569; margin: 0 0 8px 0;">We sincerely apologize for this
                        inconvenience and hope to assist you in finding the perfect vendor for your event.</p>
                    <p style="font-size: 15px; color: #475569; margin: 0;">Best regards,<br>The Eventory Team</p>
                </td>
            </tr>
        </table>

        <!-- Support Section -->
        <table width="100%" cellpadding="0" cellspacing="0"
            style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
            <tr>
                <td style="padding: 20px 30px;">
                    <p style="color: #64748b; font-size: 13px; margin: 0 0 8px 0; font-weight: 500;">Need Immediate
                        Assistance?</p>
                    <p style="color: #475569; font-size: 13px; margin: 0 0 12px 0;">
                        Our customer support team is here to help you find a replacement vendor or answer any questions
                        about this cancellation.
                    </p>
                    <a href="mailto:support@eventory.com"
                        style="color: #ef4444; text-decoration: none; font-size: 13px; font-weight: 500;">Contact
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
