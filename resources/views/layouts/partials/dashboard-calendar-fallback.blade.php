{{-- Month grid when React has not mounted. Optional holidayPayload: array{ph: array, au: array} from PublicHolidayService. --}}
@php
    $holidayPayload = $holidayPayload ?? ['ph' => [], 'au' => []];
    $holidaysByDate = [];
    foreach ($holidayPayload['ph'] ?? [] as $h) {
        if (! is_array($h) || empty($h['date'])) {
            continue;
        }
        $d = substr((string) $h['date'], 0, 10);
        $holidaysByDate[$d]['ph'] = true;
    }
    foreach ($holidayPayload['au'] ?? [] as $h) {
        if (! is_array($h) || empty($h['date'])) {
            continue;
        }
        $d = substr((string) $h['date'], 0, 10);
        $holidaysByDate[$d]['au'] = true;
    }
    $viewStart = now()->startOfMonth();
    $year = (int) $viewStart->year;
    $monthIndex = (int) $viewStart->month - 1;
    $monthNum = (int) $viewStart->month;
    $monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    $firstDay = (int) $viewStart->copy()->startOfMonth()->dayOfWeek;
    $daysInMonth = (int) $viewStart->daysInMonth;
    $daysInPrevMonth = (int) $viewStart->copy()->subMonth()->endOfMonth()->day;
    $cells = [];
    for ($i = 0; $i < $firstDay; $i++) {
        $cells[] = ['day' => $daysInPrevMonth - $firstDay + $i + 1, 'inMonth' => false];
    }
    for ($d = 1; $d <= $daysInMonth; $d++) {
        $cells[] = ['day' => $d, 'inMonth' => true];
    }
    $remaining = 42 - count($cells);
    for ($d = 1; $d <= $remaining; $d++) {
        $cells[] = ['day' => $d, 'inMonth' => false];
    }
    $today = now()->startOfDay();
    $todayY = (int) $today->year;
    $todayM = (int) $today->month - 1;
    $todayD = (int) $today->day;
@endphp
<div class="dashboard-calendar">
    <div class="dashboard-calendar-header">
        <span class="dashboard-calendar-nav inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-400 opacity-50 dark:border-slate-600 dark:bg-slate-700/50" aria-hidden="true">‹</span>
        <span class="dashboard-calendar-title">{{ $monthNames[$monthIndex] }} {{ $year }}</span>
        <span class="dashboard-calendar-nav inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-400 opacity-50 dark:border-slate-600 dark:bg-slate-700/50" aria-hidden="true">›</span>
    </div>
    <div class="dashboard-calendar-body">
        <div class="dashboard-calendar-weekdays">
            @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dow)
                <div class="dashboard-calendar-weekday">{{ $dow }}</div>
            @endforeach
        </div>
        <div class="dashboard-calendar-grid dashboard-calendar-grid--animate">
            @foreach ($cells as $index => $cell)
                @php
                    $inMonth = $cell['inMonth'];
                    $dayNum = $cell['day'];
                    $isSelected = $inMonth && $year === $todayY && $monthIndex === $todayM && $dayNum === $todayD;
                    $dateKey = $inMonth ? sprintf('%04d-%02d-%02d', $year, $monthNum, $dayNum) : '';
                    $hi = $dateKey !== '' && isset($holidaysByDate[$dateKey]) ? $holidaysByDate[$dateKey] : null;
                    $hasPh = $hi['ph'] ?? false;
                    $hasAu = $hi['au'] ?? false;
                    $cellClass = 'dashboard-calendar-cell';
                    if (!$inMonth) {
                        $cellClass .= ' other-month';
                    }
                    if ($isSelected) {
                        $cellClass .= ' selected';
                    }
                    if ($hasPh && $hasAu) {
                        $cellClass .= ' holiday holiday--both';
                    } elseif ($hasPh) {
                        $cellClass .= ' holiday holiday--ph';
                    } elseif ($hasAu) {
                        $cellClass .= ' holiday holiday--au';
                    }
                @endphp
                <span class="{{ $cellClass }}" role="gridcell">{{ $dayNum }}</span>
            @endforeach
        </div>
    </div>
</div>
