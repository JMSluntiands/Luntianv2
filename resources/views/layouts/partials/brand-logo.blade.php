@php
    $compact = $compact ?? false;
@endphp
@if($compact)
    <span class="inline-block text-lg font-extrabold tracking-[0.14em] text-[#f5a623]" role="img" aria-label="Luntian">LUNTIAN</span>
@else
    <div class="mx-auto w-full max-w-[280px] text-center" role="img" aria-label="Luntian Residential Building Design Solutions">
        <div class="text-[1.85rem] font-extrabold leading-none tracking-[0.14em] text-[#f5a623]">LUNTIAN</div>
        <div class="mt-2 text-[0.65rem] font-medium tracking-[0.04em] text-slate-500 dark:text-slate-400">Residential Building Design Solutions</div>
        <div class="mt-2.5 bg-[#f5a623] px-2 py-1.5 text-[0.55rem] font-semibold uppercase tracking-[0.12em] text-white">• Energy • Building Design • VR • AR</div>
    </div>
@endif
