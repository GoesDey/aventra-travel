@props(['variant' => 'default'])

@php
    $currentLocale = app()->getLocale();
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full border border-outline-variant/40 bg-surface-container-low/70 p-0.5 text-xs font-semibold']) }} role="group" aria-label="{{ __('Language Switcher') }}">
    <a href="{{ route('locale.switch', 'en') }}"
       class="px-2.5 py-1 rounded-full transition-colors {{ $currentLocale === 'en' ? 'bg-tropical-teal text-white shadow-xs' : 'text-on-surface-variant hover:text-oceanic-deep' }}">
        EN
    </a>
    <span class="text-outline/50 px-0.5 select-none" aria-hidden="true">|</span>
    <a href="{{ route('locale.switch', 'id') }}"
       class="px-2.5 py-1 rounded-full transition-colors {{ $currentLocale === 'id' ? 'bg-tropical-teal text-white shadow-xs' : 'text-on-surface-variant hover:text-oceanic-deep' }}">
        ID
    </a>
</div>

