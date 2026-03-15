{{-- Select2: walang hover highlight – normal at highlighted option pareho ang itsura. --}}
<style>
/* Huwag mag-highlight kapag dinadaanan ang option (dark) */
html[data-theme="dark"] .select2-dropdown .select2-results__option--highlighted,
html[data-theme="dark"] .select2-results__option--highlighted {
    background-color: transparent !important;
    color: #e2e8f0 !important;
}
/* Dark mode: selected option – dark grey bg (hindi green) */
html[data-theme="dark"] .select2-dropdown .select2-results__option[aria-selected=true] {
    background-color: #334155 !important;
    color: #f1f5f9 !important;
}
/* Light mode: visible hover – light grey para makita */
html:not([data-theme="dark"]) .select2-dropdown .select2-results__option--highlighted,
[data-theme="light"] .select2-dropdown .select2-results__option--highlighted {
    background-color: #e2e8f0 !important;
    color: #1e293b !important;
}
</style>
