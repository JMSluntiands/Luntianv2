@if(($layoutView ?? '') === 'layouts.public-form')
<span class="standalone-required-mark text-red-500{{ empty(($standaloneRequired ?? [])[$field ?? '']) ? ' hidden' : '' }}" data-required-mark="{{ $field }}" aria-hidden="true">*</span>
@endif
