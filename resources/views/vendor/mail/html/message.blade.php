@component('mail::layout')
{{-- Header --}}
@slot('header')

@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} Forum Secretari. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent
