{{-- Global Select2 theme (LBS-style). Use class select2-single or select2-multiple and call .select2() – one design app-wide. --}}
<style>
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--multiple {
        background: #1e293b !important;
        border: 1px solid #334155 !important;
        border-radius: 10px !important;
        min-height: 44px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #e2e8f0 !important;
        line-height: 42px !important;
        padding-left: 14px !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        padding: 6px 10px !important;
    }
    .select2-container--default .select2-selection__placeholder {
        color: #64748b !important;
    }
    .select2-container--default .select2-selection__arrow {
        height: 42px !important;
    }
    .select2-container--default .select2-selection__arrow b {
        border-color: #94a3b8 transparent transparent !important;
    }
    .select2-container--default .select2-selection__clear {
        display: none !important;
    }
    .select2-dropdown {
        background: #0f172a !important;
        border: 1px solid #334155 !important;
        border-radius: 10px !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2) !important;
        z-index: 10001 !important;
    }
    .select2-results .select2-results__options,
    .select2-container--default .select2-results .select2-results__options {
        background: #0f172a !important;
    }
    .select2-container--default .select2-results__option,
    .select2-dropdown .select2-results__option,
    .select2-container--default .select2-results .select2-results__option {
        color: #fff !important;
        padding: 10px 14px !important;
    }
    /* Hover at active/selected: background #1e3a5f (override Select2 default #ddd) */
    .select2-container--default .select2-results__option--highlighted,
    .select2-container--default .select2-results__option--highlighted[aria-selected],
    .select2-container--default .select2-results__option--selected,
    .select2-container--default .select2-results__option[aria-selected=true],
    .select2-dropdown .select2-results__option--highlighted,
    .select2-dropdown .select2-results__option--selected,
    .select2-dropdown .select2-results__option[aria-selected=true],
    .select2-results__option.select2-results__option--highlighted,
    .select2-results__option.select2-results__option--selected,
    .select2-results__option[aria-selected=true] {
        background: #1e3a5f !important;
        color: #fff !important;
    }
    /* Dark: lahat ng option white text; hover/selected same blue bg */
    html:not([data-theme="light"]) .select2-dropdown .select2-results__option,
    html:not([data-theme="light"]) .select2-container--default .select2-results__option {
        color: #fff !important;
    }
    html:not([data-theme="light"]) .select2-dropdown .select2-results__option--highlighted,
    html:not([data-theme="light"]) .select2-dropdown .select2-results__option--selected,
    html:not([data-theme="light"]) .select2-dropdown .select2-results__option[aria-selected=true],
    html:not([data-theme="light"]) .select2-container--default .select2-results__option--highlighted,
    html:not([data-theme="light"]) .select2-container--default .select2-results__option--selected,
    html:not([data-theme="light"]) .select2-container--default .select2-results__option[aria-selected=true] {
        background: #1e3a5f !important;
        color: #fff !important;
    }
    .select2-container--default .select2-search--dropdown {
        padding: 8px 10px !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        background: #1e293b !important;
        border: 1px solid #334155 !important;
        color: #e2e8f0 !important;
        border-radius: 8px !important;
        min-height: 40px !important;
        padding: 10px 14px !important;
        font-size: 0.9375rem !important;
    }
    .select2-container .select2-selection--multiple .select2-selection__choice {
        background: #334155 !important;
        border: none !important;
        color: #e2e8f0 !important;
        border-radius: 6px !important;
        padding: 4px 10px !important;
    }
    .select2-container .select2-selection--multiple .select2-selection__choice__remove {
        display: none !important;
    }
    /* Light theme */
    html[data-theme="light"] .select2-container--default .select2-selection--single,
    html[data-theme="light"] .select2-container--default .select2-selection--multiple {
        background: #f8fafc !important;
        border-color: #e2e8f0 !important;
    }
    html[data-theme="light"] .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #1e293b !important;
    }
    html[data-theme="light"] .select2-dropdown {
        background: #fff !important;
        border-color: #e2e8f0 !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
    }
    html[data-theme="light"] .select2-container--default .select2-results__option {
        color: #334155 !important;
    }
    /* Light theme: hover at selected = #1e3a5f (light tint) */
    html[data-theme="light"] .select2-container--default .select2-results__option--highlighted,
    html[data-theme="light"] .select2-container--default .select2-results__option--selected,
    html[data-theme="light"] .select2-container--default .select2-results__option--highlighted[aria-selected],
    html[data-theme="light"] .select2-container--default .select2-results__option[aria-selected=true] {
        background: rgba(30,58,95,0.25) !important;
        color: #1e40af !important;
    }
    html[data-theme="light"] .select2-container--default .select2-results__option--highlighted[aria-selected=true],
    html[data-theme="light"] .select2-container--default .select2-results__option[aria-selected=true].select2-results__option--highlighted {
        background: rgba(30,58,95,0.25) !important;
        color: #1e40af !important;
    }
    html[data-theme="light"] .select2-container--default .select2-search--dropdown .select2-search__field {
        background: #f8fafc !important;
        border-color: #e2e8f0 !important;
        color: #1e293b !important;
    }
    html[data-theme="light"] .select2-container .select2-selection--multiple .select2-selection__choice {
        background: #e2e8f0 !important;
        color: #334155 !important;
    }
</style>
