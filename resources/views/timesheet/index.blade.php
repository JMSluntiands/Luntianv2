@extends('layouts.dashboard')

@section('title', 'Timesheet')

@section('body_class', 'page-timesheet')

@section('content')
@php
    $displayName = static function ($user): string {
        if (! $user) {
            return 'User';
        }
        $name = trim((string) ($user->fullname ?? ''));
        if ($name !== '') {
            return $name;
        }
        $username = trim((string) ($user->username ?? ''));

        return $username !== '' ? $username : (trim((string) ($user->email ?? '')) ?: 'User');
    };
@endphp

    <div class="w-full max-w-full space-y-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">Timesheet</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Team attendance &amp; leave — green cells show hours worked</p>
            </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('timesheet') }}" class="flex items-center gap-2" id="timesheetUserFilter">
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="month" value="{{ $month }}">
                <label for="timesheetUser" class="sr-only">User</label>
                <div class="relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    <select
                        id="timesheetUser"
                        name="user"
                        onchange="this.form.submit()"
                        class="cursor-pointer appearance-none rounded-lg border border-slate-200 bg-white py-2 pl-9 pr-9 text-sm font-medium text-slate-700 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200"
                    >
                        <option value="all" @selected($selectedUser === 'all')>All team</option>
                        @foreach($userOptions as $opt)
                            <option value="{{ $opt->id }}" @selected($selectedUser === (string) $opt->id)>{{ $displayName($opt) }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <div class="flex flex-col items-start sm:items-end">
                <div class="flex items-center gap-1">
                    <a
                        href="{{ route('timesheet', ['year' => $prevYear, 'month' => $prevMonth, 'user' => $selectedUser]) }}"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                        aria-label="Previous month"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <span class="min-w-[9.5rem] text-center text-base font-semibold text-slate-800 dark:text-slate-100">{{ $monthLabel }}</span>
                    <a
                        href="{{ route('timesheet', ['year' => $nextYear, 'month' => $nextMonth, 'user' => $selectedUser]) }}"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                        aria-label="Next month"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <p class="mt-0.5 px-1 text-xs text-slate-400 dark:text-slate-500">{{ $rangeLabel }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/70">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[64rem] border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700">
                            <th class="sticky left-0 z-20 min-w-[13rem] bg-white px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                Team
                            </th>
                            @foreach($days as $day)
                                <th class="min-w-[2.35rem] px-0.5 pb-0 pt-2 text-center text-[10px] font-medium {{ $day['is_weekend'] ? 'bg-slate-50 text-slate-400 dark:bg-slate-900/40 dark:text-slate-500' : 'text-slate-400 dark:text-slate-500' }}">
                                    {{ $day['dow'] }}
                                </th>
                            @endforeach
                        </tr>
                        <tr class="border-b border-slate-200 dark:border-slate-700">
                            <th class="sticky left-0 z-20 bg-white px-4 py-1 dark:bg-slate-800"></th>
                            @foreach($days as $day)
                                <th class="px-0.5 pb-2 pt-0 text-center {{ $day['is_weekend'] ? 'bg-slate-50 dark:bg-slate-900/40' : '' }}">
                                    @if($day['is_today'])
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-sky-600 text-[11px] font-semibold text-white">{{ $day['day'] }}</span>
                                    @else
                                        <span class="inline-flex h-6 w-6 items-center justify-center text-[11px] font-medium {{ $day['in_month'] ? 'text-slate-500 dark:text-slate-400' : 'text-slate-300 dark:text-slate-600' }}">{{ $day['day'] }}</span>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($team as $member)
                            <tr class="border-b border-slate-100 last:border-b-0 dark:border-slate-700/70">
                                <td class="sticky left-0 z-10 bg-white px-4 py-2.5 dark:bg-slate-800">
                                    <div class="flex min-w-0 items-center gap-2.5">
                                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center overflow-hidden rounded-full bg-sky-600 text-[11px] font-bold text-white">
                                            @if(!empty($member['avatar']))
                                                <img src="{{ $member['avatar'] }}" alt="" class="h-full w-full object-cover">
                                            @else
                                                {{ $member['initials'] }}
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100" title="{{ $member['name'] }}">{{ $member['name'] }}</p>
                                            <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400">{{ $member['leave_credits'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                @foreach($days as $day)
                                    @php
                                        $onLeave = isset($member['leave'][$day['date']]);
                                        $leaveType = $onLeave ? $member['leave'][$day['date']] : null;
                                        $att = $member['hours'][$day['date']] ?? null;
                                        $workedHours = is_array($att) ? (float) ($att['hours'] ?? 0) : null;
                                        $noClockOut = is_array($att) && ! empty($att['no_clock_out']);
                                        $late = is_array($att) && ! empty($att['late']);
                                        $colors = is_array($att) && ! empty($att['colors']) && is_array($att['colors'])
                                            ? array_values($att['colors'])
                                            : ['green'];
                                        $shadeByColor = [
                                            'green' => 'bg-emerald-500',
                                            'orange' => 'bg-orange-500',
                                            'red' => 'bg-red-500',
                                        ];
                                        $hoursLabel = null;
                                        if ($workedHours !== null) {
                                            if ($noClockOut) {
                                                $hoursLabel = 'NC';
                                            } else {
                                                $hoursLabel = fmod($workedHours, 1.0) < 0.05
                                                    ? (string) (int) round($workedHours)
                                                    : number_format($workedHours, 1);
                                            }
                                        }
                                        $flags = [];
                                        if ($late) {
                                            $flags[] = 'Late (after 8:15 AM)';
                                        }
                                        if ($noClockOut) {
                                            $flags[] = 'No clock out';
                                        } elseif (is_array($att) && ! empty($att['open'])) {
                                            $flags[] = 'Ongoing';
                                        }
                                        $attTitle = $day['date'];
                                        if ($onLeave) {
                                            $attTitle = ucfirst((string) $leaveType).' · '.$day['date'];
                                        } elseif (is_array($att)) {
                                            $attTitle = 'In '.$att['clocked_in']
                                                .(! empty($att['clocked_out']) ? ' · Out '.$att['clocked_out'] : '')
                                                .' · '.number_format((float) $workedHours, 1).'h / 24h'
                                                .($flags !== [] ? ' · '.implode(' · ', $flags) : '');
                                        }
                                    @endphp
                                    <td class="px-0.5 py-1.5 text-center align-middle {{ $day['is_weekend'] ? 'bg-slate-50 dark:bg-slate-900/40' : '' }}" title="{{ $attTitle }}">
                                        @if($onLeave)
                                            <span class="mx-auto inline-flex h-9 w-7 items-center justify-center rounded-md bg-amber-400/90 text-[10px] font-bold uppercase text-amber-950">
                                                {{ mb_strtoupper(mb_substr((string) $leaveType, 0, 1)) }}
                                            </span>
                                        @elseif($workedHours !== null)
                                            @php
                                                $barColors = ['green'];
                                                if ($late) {
                                                    $barColors[] = 'orange';
                                                }
                                                if ($noClockOut) {
                                                    $barColors[] = 'red';
                                                }
                                            @endphp
                                            {{-- Full-height vertical (patayo) status stripes — no top dim / bottom bar --}}
                                            <span class="relative mx-auto flex h-11 w-10 overflow-hidden rounded-md shadow-sm ring-1 ring-slate-700/80">
                                                @foreach($barColors as $c)
                                                    <span class="h-full min-w-0 flex-1 {{ $shadeByColor[$c] ?? 'bg-emerald-500' }}" aria-hidden="true"></span>
                                                @endforeach
                                                <span class="pointer-events-none absolute inset-0 z-[1] flex items-center justify-center text-[13px] font-bold tabular-nums text-white drop-shadow-[0_1px_2px_rgba(0,0,0,0.85)]">
                                                    {{ $hoursLabel }}
                                                </span>
                                            </span>
                                        @elseif($day['is_today'])
                                            <span class="mx-auto inline-flex h-9 w-7 items-center justify-center rounded-full bg-sky-600 text-[11px] font-semibold text-white">{{ $day['day'] }}</span>
                                        @else
                                            <span class="mx-auto inline-flex h-9 w-7 items-center justify-center text-[11px] {{ $day['in_month'] ? 'text-slate-300 dark:text-slate-600' : 'text-slate-200 dark:text-slate-700' }}">{{ $day['day'] }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($days) + 1 }}" class="px-6 py-16 text-center text-sm text-slate-500 dark:text-slate-400">
                                    No team members found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <p class="text-xs text-slate-400 dark:text-slate-500">
            Status stripes are <strong>vertical (patayo)</strong>:
            <span class="font-medium text-emerald-600 dark:text-emerald-400">Green</span> hours,
            <span class="font-medium text-orange-500">Orange</span> late,
            <span class="font-medium text-red-500">Red</span> no clock out.
            Number = hours worked (<span class="font-medium text-red-500">NC</span> = no clock out).
        </p>
    </div>
@endsection
