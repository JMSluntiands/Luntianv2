@if(($layoutView ?? '') === 'layouts.public-form' && array_key_exists($field ?? '', $standaloneVisible ?? []) && empty(($standaloneVisible ?? [])[$field ?? '']))standalone-off @endif
