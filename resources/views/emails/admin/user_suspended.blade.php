<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Suspended - Eventory</title>
</head>

<body
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #f8fafc; color: #334155; margin:0; padding:0; line-height: 1.6;">
    <!-- Preheader Text -->
    <div style="display: none; max-height: 0; overflow: hidden;">
        Your Eventory account has been suspended
        {{ $suspensionType === 'temporary' ? 'until ' . $user->suspended_until->format('F j, Y') : 'indefinitely' }} due
        to
        violations of our policies.
    </div>

    <div
        style="max-width:600px; margin:0 auto; background:#fff; border-radius:8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
        <!-- Header -->
        <table width="100%" cellpadding="0" cellspacing="0"
            style="background: linear-gradient(135deg, #f59e0b, #fbbf24);">
            <tr>
                <td style="padding: 30px 20px; text-align: center;">
                    <h1
                        style="color: #ffffff; font-size: 28px; font-weight: 600; margin: 0 0 8px 0; letter-spacing: -0.5px;">
                        Eventory</h1>
                </td>
            </tr>
        </table>

        <!-- Suspension Badge -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background: #fffbeb;">
            <tr>
                <td style="padding: 20px; text-align: center;">
                    <div
                        style="display: inline-block; background: #f59e0b; color: #ffffff; padding: 12px 24px; border-radius: 24px; font-weight: 600; font-size: 14px;">
                        ⚠️ Account Suspended
                        @if ($suspensionType === 'temporary')
                            - Until {{ $user->suspended_until->format('F j, Y') }}
                        @else
                            - Indefinitely
                        @endif
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
                        Your Eventory account has been suspended
                        @if ($suspensionType === 'temporary')
                            until <strong>{{ $user->suspended_until->format('F j, Y') }}</strong>
                        @else
                            <strong>indefinitely</strong>
                        @endif
                        due to violations of our platform policies.
                    </p>

                    <!-- Suspension Details Card -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin: 0 0 32px 0;">
                        <tr>
                            <td style="padding: 24px;">
                                <h2
                                    style="color: #1e293b; font-size: 18px; font-weight: 600; margin: 0 0 20px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
                                    Suspension Details</h2>

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
                                            Suspension Date:</td>
                                        <td style="padding: 8px 0; color: #1e293b; font-size: 14px;">
                                            {{ $suspendedAt->format('F j, Y') }}</td>
                                    </tr>
                                    @if ($suspensionType === 'temporary')
                                        <tr>
                                            <td width="140"
                                                style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                                Suspended Until:</td>
                                            <td
                                                style="padding: 8px 0; color: #1e293b; font-size: 14px; font-weight: 600;">
                                                {{ $user->suspended_until->format('F j, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td width="140"
                                                style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                                Duration:</td>
                                            <td style="padding: 8px 0; color: #1e293b; font-size: 14px;">
                                                {{ $suspensionDuration }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td width="140"
                                            style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                            Status:</td>
                                        <td style="padding: 8px 0;">
                                            <span
                                                style="background: #fef3c7; color: #d97706; padding: 6px 14px; border-radius: 16px; font-size: 12px; font-weight: 600; display: inline-block;">
                                                @if ($suspensionType === 'temporary')
                                                    Temporarily Suspended
                                                @else
                                                    Indefinitely Suspended
                                                @endif
                                            </span>
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
                        style="background: #fffbeb; border: 1px solid #fcd34d; border-radius: 8px; margin: 0 0 32px 0;">
                        <tr>
                            <td style="padding: 20px;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td width="40" style="vertical-align: top;">
                                            <div
                                                style="background: #f59e0b; color: #ffffff; width: 24px; height: 24px; border-radius: 50%; text-align: center; line-height: 24px; font-size: 14px; font-weight: bold;">
                                                !</div>
                                        </td>
                                        <td style="padding-left: 12px;">
                                            <p
                                                style="color: #92400e; font-size: 14px; font-weight: 600; margin: 0 0 8px 0;">
                                                Reason for Suspension</p>
                                            <p style="color: #78350f; font-size: 13px; margin: 0; line-height: 1.5;">
                                                {{ $suspensionReason }}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- Restrictions Section -->
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 32px 0;">
                        <tr>
                            <td>
                                <h3 style="color: #1e293b; font-size: 16px; font-weight: 600; margin: 0 0 16px 0;">
                                    During Suspension Period</h3>

                                <table width="100%" cellpadding="0" cellspacing="0"
                                    style="background: #f8fafc; border-radius: 6px; padding: 16px;">
                                    <tr>
                                        <td>
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                @if ($role === 'customer')
                                                    <tr>
                                                        <td width="24" style="vertical-align: top; padding: 4px 0;">
                                                            <span style="color: #f59e0b; font-size: 16px;">•</span>
                                                        </td>
                                                        <td
                                                            style="padding: 4px 0 4px 8px; color: #475569; font-size: 14px;">
                                                            Cannot book new services or make reservations
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="24" style="vertical-align: top; padding: 4px 0;">
                                                            <span style="color: #f59e0b; font-size: 16px;">•</span>
                                                        </td>
                                                        <td
                                                            style="padding: 4px 0 4px 8px; color: #475569; font-size: 14px;">
                                                            Cannot contact vendors or send messages
                                                        </td>
                                                    </tr>
                                                @elseif($role === 'vendor')
                                                    <tr>
                                                        <td width="24" style="vertical-align: top; padding: 4px 0;">
                                                            <span style="color: #f59e0b; font-size: 16px;">•</span>
                                                        </td>
                                                        <td
                                                            style="padding: 4px 0 4px 8px; color: #475569; font-size: 14px;">
                                                            Cannot receive new booking requests
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="24" style="vertical-align: top; padding: 4px 0;">
                                                            <span style="color: #f59e0b; font-size: 16px;">•</span>
                                                        </td>
                                                        <td
                                                            style="padding: 4px 0 4px 8px; color: #475569; font-size: 14px;">
                                                            Services are temporarily hidden from search results
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td width="24" style="vertical-align: top; padding: 4px 0;">
                                                        <span style="color: #f59e0b; font-size: 16px;">•</span>
                                                    </td>
                                                    <td
                                                        style="padding: 4px 0 4px 8px; color: #475569; font-size: 14px;">
                                                        Limited access to account dashboard
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="24" style="vertical-align: top; padding: 4px 0;">
                                                        <span style="color: #f59e0b; font-size: 16px;">•</span>
                                                    </td>
                                                    <td
                                                        style="padding: 4px 0 4px 8px; color: #475569; font-size: 14px;">
                                                        Cannot update profile information
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- Next Steps Section -->
                    @if ($suspensionType === 'temporary')
                        <table width="100%" cellpadding="0" cellspacing="0"
                            style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; margin: 0 0 24px 0;">
                            <tr>
                                <td style="padding: 20px;">
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td width="40" style="vertical-align: top;">
                                                <div
                                                    style="background: #10b981; color: #ffffff; width: 24px; height: 24px; border-radius: 50%; text-align: center; line-height: 24px; font-size: 14px; font-weight: bold;">
                                                    🔄</div>
                                            </td>
                                            <td style="padding-left: 12px;">
                                                <p
                                                    style="color: #065f46; font-size: 14px; font-weight: 600; margin: 0 0 4px 0;">
                                                    Account Restoration</p>
                                                <p
                                                    style="color: #047857; font-size: 13px; margin: 0; line-height: 1.5;">
                                                    Your account will be automatically restored on
                                                    <strong>{{ $user->suspended_until->format('F j, Y') }}</strong>.
                                                    You'll receive an email confirmation when your account access is
                                                    reinstated.
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    @else
                        <table width="100%" cellpadding="0" cellspacing="0"
                            style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; margin: 0 0 24px 0;">
                            <tr>
                                <td style="padding: 20px;">
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td width="40" style="vertical-align: top;">
                                                <div
                                                    style="background: #ef4444; color: #ffffff; width: 24px; height: 24px; border-radius: 50%; text-align: center; line-height: 24px; font-size: 14px; font-weight: bold;">
                                                    ⚠️</div>
                                            </td>
                                            <td style="padding-left: 12px;">
                                                <p
                                                    style="color: #991b1b; font-size: 14px; font-weight: 600; margin: 0 0 4px 0;">
                                                    Indefinite Suspension</p>
                                                <p
                                                    style="color: #7f1d1d; font-size: 13px; margin: 0; line-height: 1.5;">
                                                    This suspension has no predetermined end date. To request account
                                                    reinstatement,
                                                    you must follow the appeal process outlined below.
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    @endif

                    <!-- Appeal Process -->
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 24px 0;">
                        <tr>
                            <td>
                                <h3 style="color: #1e293b; font-size: 16px; font-weight: 600; margin: 0 0 12px 0;">
                                    Appeal Process</h3>
                                <ul
                                    style="color: #475569; font-size: 14px; margin: 0; padding-left: 20px; line-height: 1.6;">
                                    <li style="margin-bottom: 8px;">Review our <a href=""
                                            style="color: #4f46e5; text-decoration: none;">Community Guidelines</a> and
                                        <a href="" style="color: #4f46e5; text-decoration: none;">Terms of
                                            Service</a>
                                    </li>
                                    <li style="margin-bottom: 8px;">Submit a formal appeal through our <a
                                            href="{{ route('appeal.form') }}"
                                            style="color: #4f46e5; text-decoration: none;">Appeal Form</a></li>
                                    <li style="margin-bottom: 8px;">Provide a detailed explanation of how you'll comply
                                        with our policies moving forward</li>
                                    <li>Appeals are typically reviewed within 5-7 business days</li>
                                </ul>
                            </td>
                        </tr>
                    </table>

                    <!-- Active Bookings Notice -->
                    {{-- @if ($activeBookingsCount > 0)
                        <table width="100%" cellpadding="0" cellspacing="0"
                            style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; margin: 0 0 24px 0;">
                            <tr>
                                <td style="padding: 20px;">
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td width="40" style="vertical-align: top;">
                                                <div
                                                    style="background: #0ea5e9; color: #ffffff; width: 24px; height: 24px; border-radius: 50%; text-align: center; line-height: 24px; font-size: 14px; font-weight: bold;">
                                                    📋</div>
                                            </td>
                                            <td style="padding-left: 12px;">
                                                <p
                                                    style="color: #0369a1; font-size: 14px; font-weight: 600; margin: 0 0 4px 0;">
                                                    Active Bookings Status</p>
                                                <p
                                                    style="color: #0c4a6e; font-size: 13px; margin: 0; line-height: 1.5;">
                                                    @if ($role === 'customer')
                                                        Your {{ $activeBookingsCount }} active booking(s) will remain
                                                        intact but you cannot make new bookings during suspension.
                                                    @else
                                                        Your {{ $activeBookingsCount }} active booking(s) will be
                                                        handled on a case-by-case basis. Customers have been notified.
                                                    @endif
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    @endif --}}

                    <!-- Closing -->
                    <p style="font-size: 15px; color: #475569; margin: 0 0 8px 0;">We take platform safety seriously
                        and appreciate your understanding.</p>
                    <p style="font-size: 15px; color: #475569; margin: 0;">Sincerely,<br>Eventory Trust & Safety Team
                    </p>
                </td>
            </tr>
        </table>

        <!-- Support Section -->
        <table width="100%" cellpadding="0" cellspacing="0"
            style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
            <tr>
                <td style="padding: 20px 30px;">
                    <p style="color: #64748b; font-size: 13px; margin: 0 0 8px 0; font-weight: 500;">Need
                        Clarification?</p>
                    <p style="color: #475569; font-size: 13px; margin: 0 0 12px 0;">
                        If you have questions about this suspension or need help with the appeal process,
                        please contact our support team.
                    </p>
                    <a href="mailto:appeals@eventory.com"
                        style="color: #f59e0b; text-decoration: none; font-size: 13px; font-weight: 500;">Contact
                        Appeals Team →</a>
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
                        This is an automated message from Eventory Trust & Safety.
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
