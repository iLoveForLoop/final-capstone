<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Account Access Closed – Eventory</title>
</head>

<body
    style="font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;background:#f8fafc;color:#334155;margin:0;padding:0;line-height:1.6;">

    <!-- Preheader Text -->
    <div style="display:none;max-height:0;overflow:hidden;">
        Notification about your Eventory account access.
    </div>

    <div
        style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.08);">

        <!-- Header -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#2563eb;">
            <tr>
                <td style="padding:28px 20px;text-align:center;">
                    <h1 style="color:#ffffff;font-size:26px;font-weight:600;margin:0;">Eventory</h1>
                </td>
            </tr>
        </table>

        <!-- Notice Banner -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#eff6ff;">
            <tr>
                <td style="padding:20px;text-align:center;">
                    <div
                        style="display:inline-block;background:#3b82f6;color:#ffffff;padding:10px 22px;border-radius:20px;font-weight:600;font-size:14px;">
                        Account Access Closed
                    </div>
                </td>
            </tr>
        </table>

        <!-- Main Content -->
        <table width="100%" cellpadding="0" cellspacing="0" style="padding:36px 30px;">
            <tr>
                <td>
                    <p style="font-size:16px;color:#475569;margin:0 0 24px 0;">Hi {{ $user->name }},</p>

                    <p style="font-size:15px;color:#475569;margin:0 0 28px 0;line-height:1.7;">
                        After a careful review, we’ve determined that your Eventory account access has been
                        <strong>closed</strong> in line with our <a href="#"
                            style="color:#2563eb;text-decoration:none;">Terms of Service</a>.
                        This action helps us maintain the safety and trust of our community.
                    </p>

                    <!-- Account Details -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;margin:0 0 28px 0;">
                        <tr>
                            <td style="padding:20px;">
                                <h2
                                    style="color:#1e293b;font-size:17px;font-weight:600;margin:0 0 16px 0;border-bottom:1px solid #e2e8f0;padding-bottom:10px;">
                                    Account Information</h2>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td width="140" style="padding:6px 0;color:#64748b;font-size:14px;">Email:
                                        </td>
                                        <td style="padding:6px 0;color:#1e293b;font-size:14px;font-weight:600;">
                                            {{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td width="140" style="padding:6px 0;color:#64748b;font-size:14px;">User
                                            Type:</td>
                                        <td style="padding:6px 0;color:#1e293b;font-size:14px;">{{ ucfirst($role) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="140" style="padding:6px 0;color:#64748b;font-size:14px;">Date:
                                        </td>
                                        <td style="padding:6px 0;color:#1e293b;font-size:14px;">
                                            {{ $banDate->format('F j, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td width="140" style="padding:6px 0;color:#64748b;font-size:14px;">Status:
                                        </td>
                                        <td style="padding:6px 0;">
                                            <span
                                                style="background:#dbeafe;color:#1e3a8a;padding:6px 14px;border-radius:16px;font-size:12px;font-weight:600;display:inline-block;">Closed</span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- Reason Section (with gentle yellow warning tone) -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background:#fefce8;border:1px solid #facc15;border-radius:8px;margin:0 0 28px 0;">
                        <tr>
                            <td style="padding:20px;">
                                <p style="color:#92400e;font-size:14px;font-weight:600;margin:0 0 6px 0;">Reason for
                                    Closure</p>
                                <p style="color:#78350f;font-size:13px;margin:0;">{{ $banReason }}</p>
                            </td>
                        </tr>
                    </table>

                    <!-- Restrictions -->
                    <h3 style="color:#1e293b;font-size:15px;font-weight:600;margin:0 0 12px 0;">Account Restrictions
                    </h3>
                    <ul style="color:#475569;font-size:14px;padding-left:20px;margin:0 0 24px 0;">
                        @if ($role === 'customer')
                            <li>Unable to make new bookings or reservations</li>
                            <li>Messaging and vendor contact features are disabled</li>
                            <li>Access to the customer dashboard is closed</li>
                        @elseif($role === 'vendor')
                            <li>All service listings are currently inactive</li>
                            <li>Cannot receive new booking requests</li>
                            <li>Vendor dashboard access is disabled</li>
                        @endif
                        <li>Login access to the account is restricted</li>
                        <li>Personal data will be permanently deleted after 30 days</li>
                    </ul>

                    <!-- Closing -->
                    <p style="font-size:15px;color:#475569;margin:0 0 8px 0;">If you believe this was an error, please
                        contact our support team for assistance.</p>
                    <p style="font-size:15px;color:#475569;margin:0;">Thank you for your
                        understanding,<br><strong>Eventory Support Team</strong></p>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#1e293b;color:#cbd5e1;">
            <tr>
                <td style="padding:24px 30px;text-align:center;">
                    <p style="font-size:12px;margin:0 0 12px 0;">
                        © {{ date('Y') }} Eventory Inc. • 123 Business Avenue, Suite 100 • New York, NY 10001
                    </p>
                    <p style="font-size:12px;margin:0 0 14px 0;">
                        <a href="#" style="color:#94a3b8;text-decoration:none;margin:0 12px;">Terms of
                            Service</a>
                        <a href="#" style="color:#94a3b8;text-decoration:none;margin:0 12px;">Privacy Policy</a>
                    </p>
                    <p style="font-size:11px;color:#64748b;margin:0;">This is an automated notification. Please do not
                        reply directly to this email.</p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
