<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUNTIAN Job Submission</title>
</head>
<body style="margin:0; padding:0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background:#f1f5f9; color:#1e293b;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#f1f5f9;">
        <tr>
            <td style="padding:24px 16px;">
                {{-- Centered card --}}
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:560px; margin:0 auto; border-radius:8px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.08); background:#ffffff;">
                    <tr>
                        <td style="padding:0;">
                            {{-- Orange header: white bold centered text --}}
                            <div style="background:#f97316; color:#ffffff; padding:14px 20px; font-size:15px; font-weight:700; text-align:center;">
                                {{ $headerTitle }}
                            </div>

                            {{-- Details table: bold labels, regular values, light grey row borders --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
                                <tr>
                                    <td style="padding:14px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; font-weight:600; color:#334155; width:140px;">LBS Ref #</td>
                                    <td style="padding:14px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; font-weight:400; color:#1e293b;">{{ $lbsRef ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; font-weight:600; color:#334155;">Client Ref #</td>
                                    <td style="padding:14px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; font-weight:400; color:#1e293b;">{{ $clientRef ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; font-weight:600; color:#334155;">Account Client</td>
                                    <td style="padding:14px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; font-weight:400; color:#1e293b;">{{ $accountClient ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; font-weight:600; color:#334155;">NCC Compliance</td>
                                    <td style="padding:14px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; font-weight:400; color:#1e293b;">{{ $nccCompliance ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; font-weight:600; color:#334155;">Job Type</td>
                                    <td style="padding:14px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; font-weight:400; color:#1e293b;">{{ $jobType ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; font-weight:600; color:#334155;">Priority</td>
                                    <td style="padding:14px 20px; border-bottom:1px solid #e2e8f0; font-size:14px; font-weight:400; color:#1e293b;">{{ $priority ?: '—' }}</td>
                                </tr>
                            </table>

                            @if($hasAttachment)
                            <div style="padding:14px 20px; font-size:13px; color:#64748b;">
                                One attachment
                            </div>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
