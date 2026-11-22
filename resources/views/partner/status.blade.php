<x-layouts.app :title="__('Partner Application Status')">
    <div class="bg-surface-container-lowest min-h-screen pt-28 pb-16 px-margin-mobile md:px-margin-desktop">
        <div class="max-w-2xl mx-auto space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-2xl font-label-bold text-sm flex items-center gap-2.5 shadow-sm">
                    <span class="material-symbols-outlined text-emerald-600 text-xl">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Status Hero Card -->
            <div class="bg-surface-white rounded-3xl border border-outline-variant/30 shadow-xl overflow-hidden">
                @if($application->status === 'pending')
                    <div class="p-8 text-center border-b border-outline-variant/25 bg-amber-50/60">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-4xl">hourglass_top</span>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-300 mb-3">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>{{ __('Pending Verification') }}</span>
                        </span>
                        <h1 class="font-display-md text-2xl font-bold text-oceanic-deep">
                            {{ __('Application Under Verification') }}
                        </h1>
                        <p class="text-sm text-on-surface-variant mt-2 max-w-lg mx-auto leading-relaxed">
                            {{ __('Thank you for applying as a TriHitaTour Travel Partner. Our Super Admin team is reviewing your business details. You will gain access to the Agent Portal once approved.') }}
                        </p>
                    </div>
                @elseif($application->status === 'rejected')
                    <div class="p-8 text-center border-b border-outline-variant/25 bg-rose-50/70">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-4xl">cancel</span>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-300 mb-3">
                            <span>{{ __('Rejected') }}</span>
                        </span>
                        <h1 class="font-display-md text-2xl font-bold text-oceanic-deep">
                            {{ __('Partner Application Rejected') }}
                        </h1>
                        <p class="text-sm text-on-surface-variant mt-2 max-w-lg mx-auto">
                            {{ __('Unfortunately, your partner application could not be approved at this time.') }}
                        </p>

                        @if($application->rejection_reason)
                            <div class="mt-5 text-left p-4 rounded-2xl bg-white border border-rose-200 max-w-lg mx-auto">
                                <div class="text-xs font-bold uppercase tracking-wider text-rose-700 mb-1">
                                    {{ __('Rejection Reason') }}
                                </div>
                                <p class="text-sm text-on-surface leading-relaxed">
                                    {{ $application->rejection_reason }}
                                </p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="p-8 text-center border-b border-outline-variant/25 bg-emerald-50/70">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-4xl">verified</span>
                        </div>
                        <h1 class="font-display-md text-2xl font-bold text-oceanic-deep">
                            {{ __('Partner Application Approved') }}
                        </h1>
                        <div class="mt-4">
                            <a href="{{ route('agent.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-tropical-teal hover:bg-oceanic-deep text-white font-label-bold text-sm transition-colors">
                                <span>{{ __('Go to Agent Dashboard') }}</span>
                                <span class="material-symbols-outlined text-base">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Submitted Business Data Summary -->
                <div class="p-6 md:p-8 space-y-5">
                    <h2 class="font-label-bold text-xs uppercase tracking-wider text-outline">{{ __('Submitted Application Summary') }}</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="p-3.5 rounded-xl bg-surface-container-lowest border border-outline-variant/25">
                            <span class="text-[11px] uppercase tracking-wider text-outline block mb-0.5">{{ __('Business Name') }}</span>
                            <span class="font-bold text-oceanic-deep">{{ $application->business_name }}</span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-surface-container-lowest border border-outline-variant/25">
                            <span class="text-[11px] uppercase tracking-wider text-outline block mb-0.5">{{ __('WhatsApp Number') }}</span>
                            <span class="font-bold text-on-surface">{{ $application->whatsapp_number }}</span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-surface-container-lowest border border-outline-variant/25">
                            <span class="text-[11px] uppercase tracking-wider text-outline block mb-0.5">{{ __('Business License / NIB') }}</span>
                            <span class="font-mono font-bold text-on-surface">{{ $application->nib_number }}</span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-surface-container-lowest border border-outline-variant/25">
                            <span class="text-[11px] uppercase tracking-wider text-outline block mb-0.5">{{ __('Bali Operating Regions') }}</span>
                            <span class="font-semibold text-on-surface">{{ implode(', ', $application->regionLabels()) }}</span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-surface-container-lowest border border-outline-variant/25 sm:col-span-2">
                            <span class="text-[11px] uppercase tracking-wider text-outline block mb-0.5">{{ __('Bank Account Details') }}</span>
                            <span class="font-bold text-on-surface">{{ $application->bank_name }} — {{ $application->bank_account_number }}</span>
                            <span class="text-xs text-outline block mt-0.5">{{ __('Account Holder Name') }}: {{ $application->bank_account_name }}</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-outline-variant/25 flex flex-wrap items-center justify-between gap-3 text-xs text-outline">
                        <span>{{ __('Submitted on:') }} {{ $application->created_at->translatedFormat('d M Y, H:i') }} WITA</span>
                        <a href="{{ route('home') }}" class="font-label-bold text-tropical-teal hover:text-oceanic-deep transition-colors">
                            {{ __('Back to Home') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

