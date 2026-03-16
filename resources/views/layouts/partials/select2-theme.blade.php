{{-- Select2: dark mode container + dropdown options --}}
<style>
/* Dark mode: Select2 closed box (selection display) */
html[data-theme="dark"] .select2-container .select2-selection--single {
    background-color: #1e293b !important;
    border-color: #475569 !important;
    color: #f1f5f9 !important;
}
html[data-theme="dark"] .select2-container .select2-selection__rendered {
    color: #f1f5f9 !important;
}
html[data-theme="dark"] .select2-container .select2-selection__arrow b {
    border-color: #94a3b8 transparent transparent transparent !important;
}
/* Dark mode: dropdown panel */
html[data-theme="dark"] .select2-dropdown {
    background-color: #1e293b !important;
    border-color: #475569 !important;
}
html[data-theme="dark"] .select2-search--dropdown .select2-search__field {
    background-color: #0f172a !important;
    border-color: #475569 !important;
    color: #f1f5f9 !important;
}
/* Huwag mag-highlight kapag dinadaanan ang option (dark) */
html[data-theme="dark"] .select2-dropdown .select2-results__option--highlighted,
html[data-theme="dark"] .select2-results__option--highlighted {
    background-color: #334155 !important;
    color: #e2e8f0 !important;
}
/* Dark mode: selected option sa dropdown */
html[data-theme="dark"] .select2-dropdown .select2-results__option[aria-selected=true] {
    background-color: #334155 !important;
    color: #f1f5f9 !important;
}
/* Light mode: visible hover */
html:not([data-theme="dark"]) .select2-dropdown .select2-results__option--highlighted,
[data-theme="light"] .select2-dropdown .select2-results__option--highlighted {
    background-color: #e2e8f0 !important;
    color: #1e293b !important;
}
</style>
