<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Update</title>
</head>
<body style="margin:0; padding:0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background:#f1f5f9; color:#1e293b;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#f1f5f9;">
        <tr>
            <td style="padding:20px 10px;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:560px; margin:0 auto; background:#ffffff; padding:2rem 1rem;">
                    <tr>
                        <td style="text-align:center; padding-bottom:1rem;">
                            @if(!empty($logoUrl))
                            <img src="{{ $logoUrl }}" alt="LUNTIAN" width="180" style="max-width:180px; height:auto; display:block; margin:0 auto 0.5rem; border:0;">
                            @endif
                            <p style="font-size:13px; color:#64748b; margin:0 0 0.25rem;">Residential Building Design Solutions</p>
                            <p style="font-size:12px; color:#f97316; margin:0 0 1.25rem; letter-spacing:0.02em;">&bull; ENERGY &bull; BUILDING DESIGN &bull; VR &bull; AR</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center; padding:0 0 0.5rem;">
                            <p style="font-size:1.25rem; font-weight:700; color:#1e293b; margin:0;">Hi there!</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center; padding:0 0 0.35rem;">
                            <p style="margin:0;"><span style="font-size:1.75rem; font-weight:800; color:#f97316;">{{ $jobReferenceNo ?: '—' }}</span></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center; padding:0 0 0.2rem;">
                            <p style="font-size:15px; color:#64748b; margin:0;">status has been updated to</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center; padding:0 0 1rem;">
                            <p style="font-size:1.125rem; font-weight:700; color:#f97316; margin:0;">{{ $jobStatus ?: '—' }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center; padding:0 0 0.25rem;">
                            <p style="font-size:14px; color:#334155; margin:0;">Assessor: {{ $assessor ?: '—' }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center; padding:0 0 1rem;">
                            <p style="font-size:14px; color:#334155; margin:0;">Assessor Email: <a href="mailto:{{ $assessorEmail ?? '' }}" style="color:#2563eb; text-decoration:none;">{{ $assessorEmail ?: '—' }}</a></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center; padding-top:1rem;">
                            <p style="font-size:14px; font-weight:700; color:#1e293b; margin:0 0 0.5rem;">Submission Notes:</p>
                            <div style="font-size:14px; color:#64748b; line-height:1.5;">
                                {!! $notes ?: '—' !!}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
