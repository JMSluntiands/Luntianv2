{{-- Compact Inter scale, matching the Archi board. Unlayered so it still applies when the Vite build is stale. --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --font-sans: "Inter", ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        --text-xs: 11px;
        --text-xs--line-height: 1.35;
        --text-sm: 13px;
        --text-sm--line-height: 1.4;
        --text-base: 13px;
        --text-base--line-height: 1.45;
        --text-lg: 15px;
        --text-lg--line-height: 1.4;
        --text-xl: 16px;
        --text-xl--line-height: 1.35;
        --text-2xl: 18px;
        --text-2xl--line-height: 1.3;
        --text-3xl: 20px;
        --text-3xl--line-height: 1.25;
        --text-4xl: 24px;
        --text-4xl--line-height: 1.2;
    }
    body {
        font-family: var(--font-sans);
        font-size: 13px;
        line-height: 1.45;
        -webkit-font-smoothing: antialiased;
    }
    table {
        font-size: 12px;
    }
    table :where(td, td *) {
        font-size: 10px;
        line-height: 1.35;
    }
    .select2-container,
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-results__option,
    .select2-search__field {
        font-family: var(--font-sans);
        font-size: 13px;
    }
    .lbs-inline-complexity {
        display: inline-flex;
        flex-wrap: nowrap;
        white-space: nowrap;
    }
    .lbs-inline-complexity .lbs-star.lbs-star-filled {
        color: rgb(251 191 36);
        cursor: pointer;
    }
    .lbs-inline-complexity .lbs-star.lbs-star-empty {
        color: rgb(100 116 139);
        opacity: 0.85;
        cursor: pointer;
    }
    .dark .lbs-inline-complexity .lbs-star.lbs-star-empty,
    [data-theme="dark"] .lbs-inline-complexity .lbs-star.lbs-star-empty {
        color: rgb(148 163 184);
    }
    .lbs-inline-complexity .lbs-star.lbs-star-filled .lbs-star-outline,
    .lbs-inline-complexity .lbs-star.lbs-star-empty .lbs-star-solid {
        display: none;
    }
    .lbs-inline-complexity .lbs-star-solid,
    .lbs-inline-complexity .lbs-star-outline {
        width: 16px;
        height: 16px;
    }
    td[data-label="Complexity"] {
        overflow: visible;
        white-space: nowrap;
    }
</style>
