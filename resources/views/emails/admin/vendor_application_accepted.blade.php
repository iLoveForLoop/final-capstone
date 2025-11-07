<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Application Accepted - Eventory</title>
</head>

<body
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #f8fafc; color: #334155; margin:0; padding:0; line-height: 1.6;">
    <!-- Preheader Text -->
    <div style="display: none; max-height: 0; overflow: hidden;">
        Congratulations! Your vendor application has been accepted by Eventory.
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
                        Vendor Application Accepted
                    </div>
                </td>
            </tr>
        </table>

        <!-- Main Content -->
        <table width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 30px;">
            <tr>
                <td>
                    <!-- Greeting -->
                    <p style="font-size: 16px; color: #475569; margin: 0 0 24px 0;">Dear {{ $vendor->business_name }},
                    </p>

                    <!-- Main Message -->
                    <p style="font-size: 15px; color: #475569; margin: 0 0 32px 0; line-height: 1.7;">
                        Congratulations! We're thrilled to inform you that your vendor application has been reviewed and
                        accepted by the Eventory team. Welcome to our community of exceptional event service providers!
                    </p>

                    <!-- Vendor Card -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin: 0 0 32px 0;">
                        <tr>
                            <td style="padding: 24px;">
                                <h2
                                    style="color: #1e293b; font-size: 18px; font-weight: 600; margin: 0 0 20px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
                                    Vendor Information</h2>

                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td width="120"
                                            style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                            Business Name:</td>
                                        <td style="padding: 8px 0; color: #1e293b; font-size: 14px; font-weight: 600;">
                                            {{ $vendor->business_name }}</td>
                                    </tr>
                                    <tr>
                                        <td width="120"
                                            style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                            Contact Person:</td>
                                        <td style="padding: 8px 0; color: #1e293b; font-size: 14px;">
                                            {{ $vendor->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td width="120"
                                            style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                            Email:</td>
                                        <td style="padding: 8px 0; color: #1e293b; font-size: 14px;">
                                            {{ $vendor->user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td width="120"
                                            style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                            Phone:</td>
                                        <td style="padding: 8px 0; color: #1e293b; font-size: 14px;">
                                            {{ $vendor->contact_number }}</td>
                                    </tr>
                                    <tr>
                                        <td width="120"
                                            style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">
                                            Status:</td>
                                        <td style="padding: 8px 0;">
                                            <span
                                                style="background: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 16px; font-size: 12px; font-weight: 600; display: inline-block;">Active
                                                Vendor</span>
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
                                                Welcome to Eventory!</p>
                                            <p style="color: #0c4a6e; font-size: 13px; margin: 0; line-height: 1.5;">
                                                Your business is now officially part of our vendor network. You can
                                                start creating service listings and receiving booking requests from
                                                customers.
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
                                <a href="{{ route('vendor.index') }}"
                                    style="display: inline-block; padding: 14px 36px; background: #10b981; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 15px; transition: all 0.2s;">
                                    Access Vendor Dashboard
                                </a>
                            </td>
                        </tr>
                    </table>

                    <!-- Next Steps -->
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 24px 0;">
                        <tr>
                            <td>
                                <h3 style="color: #1e293b; font-size: 16px; font-weight: 600; margin: 0 0 12px 0;">
                                    Getting Started</h3>
                                <ul
                                    style="color: #475569; font-size: 14px; margin: 0; padding-left: 20px; line-height: 1.6;">
                                    <li style="margin-bottom: 8px;">Complete your vendor profile with detailed
                                        information and photos</li>
                                    <li style="margin-bottom: 8px;">Create service listings to showcase what you offer
                                    </li>
                                    <li style="margin-bottom: 8px;">Set your availability and pricing for different
                                        services</li>
                                    <li>Review and respond to booking requests in your dashboard</li>
                                </ul>
                            </td>
                        </tr>
                    </table>

                    <!-- Resources -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background: #f8fafc; border-radius: 8px; padding: 20px; margin: 0 0 24px 0;">
                        <tr>
                            <td>
                                <h4 style="color: #1e293b; font-size: 15px; font-weight: 600; margin: 0 0 12px 0;">
                                    Vendor Resources</h4>
                                <p style="color: #475569; font-size: 14px; margin: 0 0 8px 0;">
                                    To help you get started, we recommend checking out our vendor resources:
                                </p>
                                <ul
                                    style="color: #475569; font-size: 14px; margin: 0; padding-left: 20px; line-height: 1.6;">
                                    <li style="margin-bottom: 4px;"><a href="#"
                                            style="color: #10b981; text-decoration: none;">Vendor Guide & Best
                                            Practices</a></li>
                                    <li style="margin-bottom: 4px;"><a href="#"
                                            style="color: #10b981; text-decoration: none;">How to Create Compelling
                                            Service Listings</a></li>
                                    <li><a href="#" style="color: #10b981; text-decoration: none;">Managing
                                            Bookings & Customer Communication</a></li>
                                </ul>
                            </td>
                        </tr>
                    </table>

                    <!-- Closing -->
                    <p style="font-size: 15px; color: #475569; margin: 0 0 8px 0;">We're excited to have you on board
                        and look forward to seeing the amazing events you'll help create!</p>
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
                        Our vendor support team is here to help you get started and answer any questions about the
                        platform.
                    </p>
                    <a href="mailto:vendors@eventory.com"
                        style="color: #10b981; text-decoration: none; font-size: 13px; font-weight: 500;">Contact
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
                        vendor notifications.
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
