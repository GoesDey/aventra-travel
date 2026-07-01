@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
            
            {{-- Mobile Pagination --}}
            <div class="flex justify-between flex-1 sm:hidden gap-2">
                <span>
                    @if ($paginator->onFirstPage())
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-label-bold text-outline bg-surface-container-lowest border border-outline-variant/20 cursor-not-allowed rounded-full">
                            {!! __('pagination.previous') !!}
                        </span>
                    @else
                        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="relative inline-flex items-center px-4 py-2 text-sm font-label-bold text-oceanic-deep bg-surface-white border border-outline-variant/30 rounded-full hover:bg-tropical-teal/10 hover:text-tropical-teal hover:border-tropical-teal/30 focus:outline-none focus:ring-2 focus:ring-tropical-teal/50 transition-colors">
                            {!! __('pagination.previous') !!}
                        </button>
                    @endif
                </span>

                <span>
                    @if ($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="relative inline-flex items-center px-4 py-2 text-sm font-label-bold text-oceanic-deep bg-surface-white border border-outline-variant/30 rounded-full hover:bg-tropical-teal/10 hover:text-tropical-teal hover:border-tropical-teal/30 focus:outline-none focus:ring-2 focus:ring-tropical-teal/50 transition-colors">
                            {!! __('pagination.next') !!}
                        </button>
                    @else
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-label-bold text-outline bg-surface-container-lowest border border-outline-variant/20 cursor-not-allowed rounded-full">
                            {!! __('pagination.next') !!}
                        </span>
                    @endif
                </span>
            </div>

            {{-- Desktop Pagination --}}
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="font-body-md text-sm text-outline">
                        <span>{!! __('Showing') !!}</span>
                        <span class="font-bold text-on-surface">{{ $paginator->firstItem() }}</span>
                        <span>{!! __('to') !!}</span>
                        <span class="font-bold text-on-surface">{{ $paginator->lastItem() }}</span>
                        <span>{!! __('of') !!}</span>
                        <span class="font-bold text-on-surface">{{ $paginator->total() }}</span>
                        <span>{!! __('results') !!}</span>
                    </p>
                </div>

                <div>
                    <span class="relative z-0 inline-flex flex-wrap gap-2 items-center">
                        <span>
                            {{-- Previous Page Link --}}
                            @if ($paginator->onFirstPage())
                                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                                    <span class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-medium text-outline bg-surface-container-lowest border border-outline-variant/20 cursor-not-allowed rounded-full" aria-hidden="true">
                                        <span class="material-symbols-outlined text-lg">chevron_left</span>
                                    </span>
                                </span>
                            @else
                                <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-medium text-oceanic-deep bg-surface-white border border-outline-variant/30 rounded-full hover:bg-tropical-teal/10 hover:text-tropical-teal hover:border-tropical-teal/30 focus:outline-none focus:ring-2 focus:ring-tropical-teal/50 transition-colors" aria-label="{{ __('pagination.previous') }}">
                                    <span class="material-symbols-outlined text-lg">chevron_left</span>
                                </button>
                            @endif
                        </span>

                        {{-- Pagination Elements --}}
                        @foreach ($elements as $element)
                            {{-- "Three Dots" Separator --}}
                            @if (is_string($element))
                                <span aria-disabled="true">
                                    <span class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-bold text-outline cursor-default">{{ $element }}</span>
                                </span>
                            @endif

                            {{-- Array Of Links --}}
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                                        @if ($page == $paginator->currentPage())
                                            <span aria-current="page">
                                                <span class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-label-bold text-surface-white bg-oceanic-deep border border-oceanic-deep cursor-default rounded-full shadow-sm">{{ $page }}</span>
                                            </span>
                                        @else
                                            <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-label-bold text-on-surface bg-surface-white border border-outline-variant/30 hover:bg-tropical-teal/10 hover:text-tropical-teal hover:border-tropical-teal/30 rounded-full focus:outline-none focus:ring-2 focus:ring-tropical-teal/50 transition-colors" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                                {{ $page }}
                                            </button>
                                        @endif
                                    </span>
                                @endforeach
                            @endif
                        @endforeach

                        <span>
                            {{-- Next Page Link --}}
                            @if ($paginator->hasMorePages())
                                <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-medium text-oceanic-deep bg-surface-white border border-outline-variant/30 rounded-full hover:bg-tropical-teal/10 hover:text-tropical-teal hover:border-tropical-teal/30 focus:outline-none focus:ring-2 focus:ring-tropical-teal/50 transition-colors" aria-label="{{ __('pagination.next') }}">
                                    <span class="material-symbols-outlined text-lg">chevron_right</span>
                                </button>
                            @else
                                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                                    <span class="relative inline-flex items-center justify-center w-10 h-10 text-sm font-medium text-outline bg-surface-container-lowest border border-outline-variant/20 cursor-not-allowed rounded-full" aria-hidden="true">
                                        <span class="material-symbols-outlined text-lg">chevron_right</span>
                                    </span>
                                </span>
                            @endif
                        </span>
                    </span>
                </div>
            </div>
        </nav>
    @endif
</div>
