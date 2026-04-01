@php
    $j = $job;
    $dateRaw = $j->date ?? $j->updated_at ?? $j->created_at ?? null;
    try {
        $dateFormatted = $dateRaw ? \Carbon\Carbon::parse($dateRaw)->timezone(config('app.timezone', 'Asia/Manila'))->format('m/d/Y') : '—';
    } catch (\Throwable) {
        $dateFormatted = $dateRaw ? (string) $dateRaw : '—';
    }
    $val = fn ($v) => filled((string) ($v ?? '')) ? e((string) $v) : '—';
    $valNl = fn ($v) => filled((string) ($v ?? '')) ? nl2br(e((string) $v)) : '—';
    $gold = '#FFD200';
    $specHeading = '#FF8C00';
    $bodyInset = '14mm';
    $hasMergeImage = filled($mergeImageDataUri ?? null);
    $hasMergePdfNote = filled($mergePdfName ?? null);
    $totalPages = 1 + (($hasMergeImage || $hasMergePdfNote) ? 1 : 0);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Compliance summary — {{ $val($j->job_number ?? null) }}</title>
    <style>
        /* Sagad sa taas: no page margin; body content uses .pdf-body inset */
        @page {
            margin: 0;
        }
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.45;
            color: #000;
        }
        .header-root {
            width: 100%;
            margin: 0;
            padding: 0;
        }
        .header-root table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        .header-root td {
            vertical-align: middle;
        }
        /* Puting strip: LUNTIAN + mas mababang gold box na katabi (hindi full height ng row) */
        .brand-left {
            background-color: #fff;
            font-size: 26pt;
            font-weight: 700;
            color: {{ $gold }};
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 14px 10px 16px {{ $bodyInset }};
            width: 1%;
            white-space: nowrap;
            line-height: 1;
        }
        .brand-right-cell {
            background-color: #fff;
            padding: 12px {{ $bodyInset }} 14px 6px;
            width: 100%;
        }
        .brand-right-box {
            display: inline-block;
            background-color: {{ $gold }};
            color: #fff;
            font-weight: 700;
            font-size: 8pt;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 9px 16px;
            line-height: 1.2;
            vertical-align: middle;
        }
        /* Dilaw na bar — DomPDF: buong lapad + text-align sa td + fixed layout para tunay na gitna */
        .bar-table {
            width: 100%;
            table-layout: fixed;
        }
        .bar-yellow {
            background-color: {{ $gold }};
            padding: 0;
            margin: 0;
            width: 100%;
            text-align: center;
        }
        .bar-line-tag {
            color: #fff;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            font-size: 7.5pt;
            letter-spacing: 0.06em;
            padding: 9px 12px 8px;
            margin: 0;
        }
        .bar-line-contact {
            color: #000;
            font-weight: 400;
            text-align: center;
            font-size: 7.5pt;
            line-height: 1.6;
            padding: 0 12px 11px;
            margin: 0;
        }
        .bar-line-contact strong {
            font-weight: 700;
        }
        .pdf-body {
            padding: 20px {{ $bodyInset }} 16px {{ $bodyInset }};
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        .meta-table td {
            vertical-align: top;
            padding: 0 0 10px 0;
        }
        .meta-table .label-cell {
            font-weight: 700;
            text-transform: uppercase;
            white-space: nowrap;
            padding-right: 10px;
            width: 1%;
        }
        .meta-table .val-cell {
            font-weight: 400;
            text-transform: none;
        }
        h1.section {
            font-size: 13pt;
            font-weight: 700;
            color: #000;
            margin: 22px 0 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        h2.spec-title {
            font-size: 10.5pt;
            font-weight: 700;
            color: {{ $specHeading }};
            margin: 20px 0 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .spec-block { margin-top: 14px; }
        .spec-label { font-weight: 700; margin-bottom: 3px; text-transform: uppercase; }
        .spec-block .spec-value { font-weight: 400; text-transform: none; }
        .page-num {
            margin-top: 32px;
            text-align: center;
            font-size: 8pt;
            color: #333;
        }
        .page-break { page-break-before: always; }
        .merge-img {
            display: block;
            max-width: 100%;
            height: auto;
            margin-top: 10px;
        }
        .muted { color: #444; font-size: 9pt; }
    </style>
</head>
<body>
    <header class="header-root">
        <table cellspacing="0" cellpadding="0">
            <tr>
                <td class="brand-left">LUNTIAN</td>
                <td class="brand-right-cell">
                    <div class="brand-right-box">BUILDING DESIGN SOLUTIONS</div>
                </td>
            </tr>
        </table>
        <table class="bar-table" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
            <tr>
                <td class="bar-yellow" align="center" width="100%" style="width:100%;text-align:center;">
                    {{-- DomPDF: i-center ang buong text block gamit ang inline-block sa loob ng text-align:center na td --}}
                    <div style="display:inline-block;text-align:center;max-width:100%;">
                        <div class="bar-line-tag">• ENERGY EFFICIENCY • BAL • BUILDING DESIGN •</div>
                        <div class="bar-line-contact">
                            <strong>A:</strong> 12 Darton Loop, Bertram WA 6167 &nbsp;
                            <strong>M:</strong> 0424933002 &nbsp;
                            <strong>E:</strong> luntiands@gmail.com &nbsp;
                            <strong>ABN:</strong> 25405733031
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </header>

    <div class="pdf-body">
        <table class="meta-table">
            <tr>
                <td class="label-cell">DATE:</td>
                <td class="val-cell">{{ $dateFormatted }}</td>
            </tr>
            <tr>
                <td class="label-cell">ADDRESS:</td>
                <td class="val-cell">{{ $val($j->address ?? null) }}</td>
            </tr>
            <tr>
                <td class="label-cell">CLIMATE ZONE:</td>
                <td class="val-cell">{{ $val($j->climate_zone ?? null) }}</td>
            </tr>
        </table>

        <h1 class="section">COMPLIANCE SUMMARY</h1>
        <div>{!! $valNl($j->compliance_summary_description ?? null) !!}</div>

        <h2 class="spec-title">SPECIFICATION REQUIREMENTS:</h2>
        <div class="spec-block"><div class="spec-label">Client No:</div><div class="spec-value">{!! $valNl($j->spec_client_no ?? null) !!}</div></div>
        <div class="spec-block"><div class="spec-label">LBS No:</div><div class="spec-value">{!! $valNl($j->spec_lbs_no ?? null) !!}</div></div>
        <div class="spec-block"><div class="spec-label">Plans:</div><div class="spec-value">{!! $valNl($j->spec_plans ?? null) !!}</div></div>
        <div class="spec-block"><div class="spec-label">Insulation:</div><div class="spec-value">{!! $valNl($j->spec_insulation ?? null) !!}</div></div>
        <div class="spec-block"><div class="spec-label">Glazing:</div><div class="spec-value">{!! $valNl($j->spec_glazing ?? null) !!}</div></div>
        <div class="spec-block"><div class="spec-label">Sealing:</div><div class="spec-value">{!! $valNl($j->spec_sealing ?? null) !!}</div></div>
        <div class="spec-block"><div class="spec-label">Services:</div><div class="spec-value">{!! $valNl($j->spec_services ?? null) !!}</div></div>
        <div class="spec-block"><div class="spec-label">Additional:</div><div class="spec-value">{!! $valNl($j->spec_additional ?? null) !!}</div></div>

        <p class="page-num">-- 1 of {{ $totalPages }} --</p>
    </div>

    @if($hasMergeImage)
        <div class="pdf-body page-break">
            <h1 class="section" style="margin-top:0;">ATTACHMENT (PRINT MERGE)</h1>
            <img src="{{ $mergeImageDataUri }}" alt="Print merge" class="merge-img">
            <p class="page-num">-- 2 of {{ $totalPages }} --</p>
        </div>
    @endif

    @if($hasMergePdfNote)
        <div class="pdf-body page-break">
            <h1 class="section" style="margin-top:0;">ATTACHED DOCUMENT</h1>
            <p class="muted">File: {{ e($mergePdfName) }}</p>
            <p style="margin-top:12px;font-size:9pt;line-height:1.45;">
                This PDF could not be embedded in this summary. Open the job in Luntian and download the print-merge file from Additional Information, or print this page together with that file.
            </p>
            <p class="page-num">-- 2 of {{ $totalPages }} --</p>
        </div>
    @endif
</body>
</html>
