@php
    $chartPayload = $dashboardStatusChart ?? ['date' => now('Asia/Manila')->toDateString(), 'branches' => []];
    $chartBranches = collect($chartPayload['branches'] ?? [])->filter(fn ($b) => (int) ($b['total'] ?? 0) > 0)->values();
    $chartMaxTotal = (int) $chartBranches->max(fn ($b) => (int) ($b['total'] ?? 0));
    $legend = [];
    foreach ($chartBranches as $branch) {
        foreach ($branch['statuses'] ?? [] as $s) {
            $legend[$s['label'] ?? ''] = $s;
        }
    }
    $shortBranch = static function (string $label): string {
        return match (strtoupper(trim($label))) {
            'GENERAL ASSEMBLY' => 'GA',
            'EFFICIENT LIVING' => 'EL',
            'FYRS ENERGY WISE' => 'FYRS',
            default => $label,
        };
    };
@endphp
<section
    id="dashboard-status-chart-fallback"
    class="dashboard-status-chart mb-6 mt-6 min-w-0 overflow-hidden rounded-xl border border-slate-700/60 bg-[#0f172a] shadow-lg"
    data-chart-api="{{ route('dashboard.chart', [], false) }}"
>
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-700/60 px-4 py-3 sm:px-5">
        <h2 class="flex items-center gap-2.5 text-sm font-semibold text-slate-100 sm:text-base">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/15 text-blue-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </span>
            Job Status Chart
        </h2>
    </div>
    <p class="px-4 py-2 text-xs text-slate-400 sm:px-5">Active jobs per module — matches Total Jobs card (status breakdown)</p>
    <div class="space-y-3 px-4 pb-6 pt-2 sm:px-5">
        @forelse ($chartBranches as $branch)
            @php
                $total = (int) ($branch['total'] ?? 0);
            @endphp
            <div class="flex items-center gap-3">
                <div class="w-[4.5rem] shrink-0 text-right text-[10px] font-semibold text-slate-300 sm:w-24 sm:text-xs" title="{{ $branch['label'] ?? '' }}">
                    {{ $shortBranch((string) ($branch['label'] ?? '')) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex h-7 w-full overflow-hidden rounded-md bg-slate-800/50 sm:h-8">
                        @foreach ($branch['statuses'] ?? [] as $status)
                            @php
                                $count = (int) ($status['count'] ?? 0);
                                $widthPct = $chartMaxTotal > 0 && $count > 0 ? ($count / $chartMaxTotal) * 100 : 0;
                                $bg = ! empty($status['color']) ? trim((string) $status['color']) : '#3b82f6';
                                if ($bg !== '' && $bg[0] !== '#') { $bg = '#'.$bg; }
                            @endphp
                            <div class="h-full shrink-0" style="width: {{ $widthPct }}%; min-width: {{ $count > 0 ? '2px' : '0' }}; background-color: {{ $bg }}" title="{{ $status['label'] ?? '' }}: {{ $count }}"></div>
                        @endforeach
                    </div>
                </div>
                <span class="w-8 shrink-0 text-right text-xs font-semibold tabular-nums text-slate-300 sm:w-10">{{ $total }}</span>
            </div>
        @empty
            <p class="text-sm text-slate-500">No active jobs in Job Management.</p>
        @endforelse
        @if (count($legend) > 0)
            <div class="mt-4 flex flex-wrap gap-2 border-t border-slate-700/60 pb-1 pt-4">
                @foreach ($legend as $item)
                    @php
                        $bg = ! empty($item['color']) ? trim((string) $item['color']) : '#3b82f6';
                        if ($bg !== '' && $bg[0] !== '#') { $bg = '#'.$bg; }
                        $fg = \App\Models\Status::resolveFontColor($item['fontColor'] ?? null);
                    @endphp
                    <span class="rounded-md px-2 py-1 text-[10px] font-medium sm:text-xs" style="background-color: {{ $bg }}; color: {{ $fg }}">{{ $item['label'] ?? '' }}</span>
                @endforeach
            </div>
        @endif
    </div>
</section>
