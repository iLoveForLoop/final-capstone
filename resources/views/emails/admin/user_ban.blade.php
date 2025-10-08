<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Permanently Banned - Eventory</title>
</head>

<body
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #f8fafc; color: #334155; margin:0; padding:0; line-height: 1.6;">
    <!-- Preheader Text -->
    <div style="display: none; max-height: 0; overflow: hidden;">
        Your Eventory account has been permanently banned due to violations of our terms of service.
    </div>

    <div
        style="max-width:600px; margin:0 auto; background:#fff; border-radius:8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
        <!-- Header -->
        <table width="100%" cellpadding="0" cellspacing="0"
            style="background: linear-gradient(135deg, #dc2626, #ef4444);">
            <tr>
                <td style="padding: 30px 20px; text-align: center;">
                    <h1
                        style="color: #ffffff; font-size: 28px; font-weight: 600; margin: 0 0 8px 0; letter-spacing: -0.5px;">
                        Eventory</h1>
                </td>
            </tr>
        </table>

        <!-- Ban Badge -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background: #fef2f2;">
            <tr>
                <td style="padding: 20px; text-align: center;">
                    <div
                        style="display: inline-block; background: #dc2626; color: #ffffff; padding: 12px 24px; border-radius: 24px; font-weight: 600; font-size: 14px;">
                        ⚠️ Account Permanently Banned
                    </div>
                </td>
            </tr>
        </table>

        <!-- Main Content -->
        <table width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 30px;">
            <tr>
                <td>
                    <!-- Greeting -->
                    <p style="font-size: 16px; color: #475569; margin: 0 0 24px 0;">Dear {{ $user->name }},</p>

                    <!-- Main Message -->
                    <p style="font-size: 15px; color: #475569; margin: 0 0 32px 0; line-height: 1.7;">
                        After careful review, we regret to inform you that your Eventory account has been
                        <strong>permanently banned</strong>
                        effective immediately. This decision is final and cannot be appealed.
                    </p>

                    <!-- Ban Details Card -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin: 0 0 32px 0;">
                        <tr>
                            <td style="padding: 24px;">
                                <h2
                                    style="color: #1e293b; font-size: 18px; font-weight: 600; margin: 0 0 20px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
                                    Account Ban Details</h2>

                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td width="140"
                                            style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                            Account:</td>
                                        <td style="padding: 8px 0; color: #1e293b; font-size: 14px; font-weight: 600;">
                                            {{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td width="140"
                                            style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                            User Type:</td>
                                        <td style="padding: 8px 0; color: #1e293b; font-size: 14px;">
                                            {{ ucfirst($role) }}</td>
                                    </tr>
                                    <tr>
                                        <td width="140"
                                            style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                            Ban Date:</td>
                                        <td style="padding: 8px 0; color: #1e293b; font-size: 14px;">
                                            {{ $banDate->format('F j, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td width="140"
                                            style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                            Status:</td>
                                        <td style="padding: 8px 0;">
                                            <span
                                                style="background: #fecaca; color: #dc2626; padding: 6px 14px; border-radius: 16px; font-size: 12px; font-weight: 600; display: inline-block;">Permanently
                                                Banned</span>
                                        </td>
                                    </tr>
                                    {{-- @if ($violationType)
                                        <tr>
                                            <td width="140"
                                                style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                                Violation:</td>
                                            <td style="padding: 8px 0; color: #1e293b; font-size: 14px;">
                                                {{ $violationType }}
                                            </td>
                                        </tr>
                                    @endif --}}
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- Reason Section -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; margin: 0 0 32px 0;">
                        <tr>
                            <td style="padding: 20px;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td width="40" style="vertical-align: top;">
                                            <div
                                                style="background: #dc2626; color: #ffffff; width: 24px; height: 24px; border-radius: 50%; text-align: center; line-height: 24px; font-size: 14px; font-weight: bold;">
                                                !</div>
                                        </td>
                                        <td style="padding-left: 12px;">
                                            <p
                                                style="color: #991b1b; font-size: 14px; font-weight: 600; margin: 0 0 8px 0;">
                                                Reason for Ban</p>
                                            <p style="color: #7f1d1d; font-size: 13px; margin: 0; line-height: 1.5;">
                                                {{ $banReason }}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- Immediate Restrictions -->
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 32px 0;">
                        <tr>
                            <td>
                                <h3 style="color: #1e293b; font-size: 16px; font-weight: 600; margin: 0 0 16px 0;">
                                    Immediate Restrictions</h3>

                                <table width="100%" cellpadding="0" cellspacing="0"
                                    style="background: #f8fafc; border-radius: 6px; padding: 16px;">
                                    <tr>
                                        <td>
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                @if ($role === 'customer')
                                                    <tr>
                                                        <td width="24" style="vertical-align: top; padding: 4px 0;">
                                                            <span style="color: #ef4444; font-size: 16px;">•</span>
                                                        </td>
                                                        <td
                                                            style="padding: 4px 0 4px 8px; color: #475569; font-size: 14px;">
                                                            Cannot book any services or make new reservations
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="24" style="vertical-align: top; padding: 4px 0;">
                                                            <span style="color: #ef4444; font-size: 16px;">•</span>
                                                        </td>
                                                        <td
                                                            style="padding: 4px 0 4px 8px; color: #475569; font-size: 14px;">
                                                            Cannot contact vendors or send messages
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="24" style="vertical-align: top; padding: 4px 0;">
                                                            <span style="color: #ef4444; font-size: 16px;">•</span>
                                                        </td>
                                                        <td
                                                            style="padding: 4px 0 4px 8px; color: #475569; font-size: 14px;">
                                                            Cannot access customer dashboard or booking history
                                                        </td>
                                                    </tr>
                                                @elseif($role === 'vendor')
                                                    <tr>
                                                        <td width="24" style="vertical-align: top; padding: 4px 0;">
                                                            <span style="color: #ef4444; font-size: 16px;">•</span>
                                                        </td>
                                                        <td
                                                            style="padding: 4px 0 4px 8px; color: #475569; font-size: 14px;">
                                                            Cannot receive new booking requests
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="24" style="vertical-align: top; padding: 4px 0;">
                                                            <span style="color: #ef4444; font-size: 16px;">•</span>
                                                        </td>
                                                        <td
                                                            style="padding: 4px 0 4px 8px; color: #475569; font-size: 14px;">
                                                            All active listings and services have been deactivated
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="24" style="vertical-align: top; padding: 4px 0;">
                                                            <span style="color: #ef4444; font-size: 16px;">•</span>
                                                        </td>
                                                        <td
                                                            style="padding: 4px 0 4px 8px; color: #475569; font-size: 14px;">
                                                            Cannot access vendor dashboard or manage existing bookings
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td width="24" style="vertical-align: top; padding: 4px 0;">
                                                        <span style="color: #ef4444; font-size: 16px;">•</span>
                                                    </td>
                                                    <td
                                                        style="padding: 4px 0 4px 8px; color: #475569; font-size: 14px;">
                                                        Cannot log in to your Eventory account
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="24" style="vertical-align: top; padding: 4px 0;">
                                                        <span style="color: #ef4444; font-size: 16px;">•</span>
                                                    </td>
                                                    <td
                                                        style="padding: 4px 0 4px 8px; color: #475569; font-size: 14px;">
                                                        All personal data will be permanently deleted after 30 days
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- Active Bookings Notice -->
                    {{-- @if ($activeBookingsCount > 0)
                        <table width="100%" cellpadding="0" cellspacing="0"
                            style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; margin: 0 0 24px 0;">
                            <tr>
                                <td style="padding: 20px;">
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td width="40" style="vertical-align: top;">
                                                <div
                                                    style="background: #d97706; color: #ffffff; width: 24px; height: 24px; border-radius: 50%; text-align: center; line-height: 24px; font-size: 14px; font-weight: bold;">
                                                    📋</div>
                                            </td>
                                            <td style="padding-left: 12px;">
                                                <p
                                                    style="color: #92400e; font-size: 14px; font-weight: 600; margin: 0 0 4px 0;">
                                                    Active Bookings Notice</p>
                                                <p
                                                    style="color: #78350f; font-size: 13px; margin: 0; line-height: 1.5;">
                                                    You have {{ $activeBookingsCount }} active booking(s) that will be
                                                    automatically
                                                    cancelled and refunded. Affected parties have been notified.
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    @endif --}}

                    <!-- Final Notice -->
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 24px 0;">
                        <tr>
                            <td>
                                <h3 style="color: #1e293b; font-size: 16px; font-weight: 600; margin: 0 0 12px 0;">
                                    Final Notice</h3>
                                <p style="color: #475569; font-size: 14px; margin: 0 0 16px 0; line-height: 1.6;">
                                    This permanent ban is the result of severe and/or repeated violations of our
                                    <a href="" style="color: #4f46e5; text-decoration: none;">Terms of
                                        Service</a>
                                    and <a href="" style="color: #4f46e5; text-decoration: none;">Community
                                        Guidelines</a>.
                                    This decision is final and cannot be appealed.
                                </p>
                                <p style="color: #475569; font-size: 14px; margin: 0; line-height: 1.6;">
                                    Attempting to create new accounts will result in immediate termination and may lead
                                    to legal action.
                                </p>
                            </td>
                        </tr>
                    </table>

                    <!-- Closing -->
                    <p style="font-size: 15px; color: #475569; margin: 0 0 8px 0;">This action was taken to maintain
                        the safety and integrity of our platform.</p>
                    <p style="font-size: 15px; color: #475569; margin: 0;">Sincerely,<br>Eventory Trust & Safety Team
                    </p>
                </td>
            </tr>
        </table>

        <!-- Legal Section -->
        <table width="100%" cellpadding="0" cellspacing="0"
            style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
            <tr>
                <td style="padding: 20px 30px;">
                    <p style="color: #64748b; font-size: 12px; margin: 0 0 8px 0; font-weight: 500;">Legal Notice</p>
                    <p style="color: #475569; font-size: 11px; margin: 0 0 12px 0; line-height: 1.4;">
                        This email constitutes official notice of account termination. All rights and privileges
                        associated with your Eventory account have been permanently revoked. Any attempt to
                        circumvent this ban may result in further legal action.
                    </p>
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
                        <a href="" style="color: #94a3b8; text-decoration: none; margin: 0 12px;">Terms of
                            Service</a>
                        <a href="" style="color: #94a3b8; text-decoration: none; margin: 0 12px;">Privacy
                            Policy</a>
                        <a href="" style="color: #94a3b8; text-decoration: none; margin: 0 12px;">Community
                            Guidelines</a>
                    </p>
                    <p style="font-size: 11px; color: #64748b; margin: 0;">
                        This is an automated message from Eventory Trust & Safety. Do not reply to this email.
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
