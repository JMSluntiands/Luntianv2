<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Compliance summary — {{ $job->job_number ?? $job->id }}</title>
    <style>
        @page { margin: 14mm 16mm; size: A4; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.45;
            color: #111;
            margin: 0;
            padding: 0 0 28px;
        }
        .no-print {
            padding: 12px 16px;
            background: #f1f5f9;
            border-bottom: 1px solid #cbd5e1;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: flex-end;
        }
        .no-print button, .no-print a.btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }
        .no-print .btn-print {
            background: #2563eb;
            color: #fff;
        }
        .no-print .btn-print:hover { background: #1d4ed8; }
        .no-print .btn-close {
            background: #fff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .sheet { max-width: 720px; margin: 0 auto; padding: 8px 12px 40px; }
        .center { text-align: center; }
        .company {
            font-weight: 700;
            font-size: 15pt;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .tagline {
            font-size: 9pt;
            margin-top: 6px;
            letter-spacing: 0.06em;
        }
        .contact {
            font-size: 8.5pt;
            margin-top: 10px;
            line-height: 1.5;
        }
        .divider { margin: 22px 0 16px; border: 0; border-top: 1px solid #ccc; }
        .row { margin-top: 12px; }
        .label { font-weight: 700; }
        h2.section {
            font-size: 11pt;
            font-weight: 700;
            margin: 20px 0 10px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .body-text {
            white-space: pre-wrap;
            word-wrap: break-word;
            margin: 0;
        }
        .spec-block { margin-top: 14px; }
        .spec-label { font-weight: 700; margin-bottom: 4px; }
        .page-num {
            margin-top: 36px;
            text-align: center;
            font-size: 9pt;
            color: #444;
        }
        .page-break { page-break-before: always; break-before: page; padding-top: 14mm; }
        .merge-img {
            display: block;
            max-width: 100%;
            height: auto;
            margin: 12px auto 0;
        }
        .merge-pdf {
            width: 100%;
            min-height: 260mm;
            border: 1px solid #ddd;
        }
        .muted { color: #555; }
        @media print {
            .no-print { display: none !important; }
            body { padding-bottom: 0; }
            .sheet { padding: 0; max-width: none; }
        }
    </style>
    @if(request()->boolean('autoprint'))
        <script>
            window.addEventListener('load', function () {
                setTimeout(function () { window.print(); }, 300);
            });
        </script>
    @endif
</head>
<body>
    <div class="no-print">
        <button type="button" class="btn-print" onclick="window.print()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z"/></svg>
            Print
        </button>
        <button type="button" class="btn-close" onclick="window.close()">Close</button>
    </div>

    @php
        $j = $job;
        $dateRaw = $j->date ?? $j->updated_at ?? $j->created_at ?? null;
        try {
            $dateFormatted = $dateRaw ? \Carbon\Carbon::parse($dateRaw)->timezone(config('app.timezone', 'Asia/Manila'))->format('m/d/Y') : '—';
        } catch (\Throwable) {
            $dateFormatted = $dateRaw ? (string) $dateRaw : '—';
        }
        $val = fn ($v) => filled((string) ($v ?? '')) ? (string) $v : '—';
        $mergeName = (string) ($j->spec_print_merge_file ?? '');
        $mergeExt = strtolower(pathinfo($mergeName, PATHINFO_EXTENSION));
        $hasMerge = $mergeName !== '';
        $isImageMerge = in_array($mergeExt, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'], true);
        $mergeUrl = $hasMerge ? route('bph.job.mergeFile', ['id' => $j->id]) : null;
        $totalPages = $hasMerge ? 2 : 1;
    @endphp

    <div class="sheet">
        <div class="center">
            <div class="company">{{ $printCompanyName ?? 'LUNTIAN BUILDING DESIGN SOLUTIONS' }}</div>
            <div class="tagline">{{ $printTagline ?? '• ENERGY EFFICIENCY • BAL • BUILDING DESIGN •' }}</div>
            <div class="contact muted">{{ $printContactLine ?? 'A: 12 Darton Loop, Bertram WA 6167 M: 0424933002 E: luntiands@gmail.com ABN: 25405733031' }}</div>
        </div>

        <hr class="divider">

        <div class="row muted" style="font-size:9pt;">JOB: {{ $val($j->job_number ?? null) }} — REF: {{ $val($j->reference ?? null) }} — CLIENT: {{ $val($j->client_name ?? null) }}</div>

        <div class="row"><span class="label">DATE:</span> {{ $dateFormatted }}</div>
        <div class="row"><span class="label">ADDRESS:</span> {{ $val($j->address ?? null) }}</div>
        <div class="row"><span class="label">CLIMATE ZONE:</span> {{ $val($j->climate_zone ?? null) }}</div>

        <h2 class="section">COMPLIANCE SUMMARY</h2>
        <div class="body-text">{{ $val($j->compliance_summary_description ?? null) }}</div>

        <h2 class="section">SPECIFICATION REQUIREMENTS:</h2>
        <div class="spec-block"><div class="spec-label">Client No:</div><div class="body-text">{{ $val($j->spec_client_no ?? null) }}</div></div>
        <div class="spec-block"><div class="spec-label">LBS No:</div><div class="body-text">{{ $val($j->spec_lbs_no ?? null) }}</div></div>
        <div class="spec-block"><div class="spec-label">Plans:</div><div class="body-text">{{ $val($j->spec_plans ?? null) }}</div></div>
        <div class="spec-block"><div class="spec-label">Insulation:</div><div class="body-text">{{ $val($j->spec_insulation ?? null) }}</div></div>
        <div class="spec-block"><div class="spec-label">Glazing:</div><div class="body-text">{{ $val($j->spec_glazing ?? null) }}</div></div>
        <div class="spec-block"><div class="spec-label">Sealing:</div><div class="body-text">{{ $val($j->spec_sealing ?? null) }}</div></div>
        <div class="spec-block"><div class="spec-label">Services:</div><div class="body-text">{{ $val($j->spec_services ?? null) }}</div></div>
        <div class="spec-block"><div class="spec-label">Additional:</div><div class="body-text">{{ $val($j->spec_additional ?? null) }}</div></div>

        <p class="page-num">-- 1 of {{ $totalPages }} --</p>
    </div>

    @if($hasMerge && $mergeUrl)
        <div class="sheet page-break">
            @if($isImageMerge)
                <h2 class="section" style="margin-top:0;">ATTACHMENT (PRINT MERGE)</h2>
                <img src="{{ $mergeUrl }}" alt="Print merge attachment" class="merge-img">
            @else
                <h2 class="section" style="margin-top:0;">ATTACHED DOCUMENT</h2>
                <p class="muted body-text" style="margin-bottom:12px;">File: {{ $mergeName }}</p>
                <embed src="{{ $mergeUrl }}#toolbar=0" type="application/pdf" class="merge-pdf" title="Attachment PDF">
                <p class="muted no-print" style="margin-top:12px;font-size:10pt;">If the preview is blank, use <strong>Print</strong> above — some browsers include the PDF in the printout; otherwise download the merge file from Job Details.</p>
            @endif
            <p class="page-num">-- 2 of 2 --</p>
        </div>
    @endif
</body>
</html>
