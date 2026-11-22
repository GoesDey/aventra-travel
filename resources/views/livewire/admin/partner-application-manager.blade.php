<div>
    <!-- Actions Bar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h2 class="font-headline-md text-xl text-oceanic-deep">{{ __('Partner Applications') }}</h2>
                @if($pendingCount > 0)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                        {{ $pendingCount }} {{ __('Pending') }}
                    </span>
                @endif
            </div>
            <p class="font-body-md text-sm text-on-surface-variant">
                {{ __('Review travel partner registrations, verify business credentials, and approve or reject applications.') }}
            </p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-auto flex items-center gap-2 bg-surface-white px-4 py-2 rounded-lg border border-outline-variant/30 shadow-sm">
                <span class="font-label-bold text-label-bold text-on-surface-variant whitespace-nowrap">{{ __('Filter Status:') }}</span>
                <select wire:model.live="statusFilter" class="bg-transparent border-none focus:ring-0 text-body-md font-body-md text-oceanic-deep font-medium cursor-pointer p-0 pr-6">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="pending">{{ __('Pending') }}</option>
                    <option value="approved">{{ __('Approved') }}</option>
                    <option value="rejected">{{ __('Rejected') }}</option>
                </select>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 text-emerald-900 border border-emerald-200 p-4 rounded-xl mb-6 font-label-bold text-sm shadow-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-emerald-600">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Applications Table -->
    <div class="bg-surface-white rounded-2xl border border-outline-variant/30 shadow-[0_10px_30px_rgba(0,35,102,0.05)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[860px]">
                <thead>
                    <tr class="bg-surface-container-lowest border-b border-outline-variant/30 text-on-surface-variant">
                        <th class="py-4 px-6 font-label-bold text-label-bold">{{ __('Business Name') }}</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold">{{ __('Applicant') }}</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold">{{ __('NIB / WhatsApp') }}</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold">{{ __('Bali Operating Regions') }}</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold text-center">{{ __('Status') }}</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold text-center">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 font-body-md text-sm">
                    @forelse($applications as $app)
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-label-bold text-oceanic-deep">{{ $app->business_name }}</div>
                                <div class="font-caption text-[11px] text-outline">{{ $app->created_at->translatedFormat('d M Y, H:i') }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-medium text-on-surface">{{ $app->user->name ?? '-' }}</div>
                                <div class="font-caption text-[11px] text-outline">{{ $app->user->email ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-mono text-xs font-semibold text-on-surface">{{ $app->nib_number }}</div>
                                <div class="font-caption text-[11px] text-outline">{{ $app->whatsapp_number }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($app->regionLabels() as $regionLabel)
                                        <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-surface-container-low text-oceanic-deep border border-outline-variant/30">
                                            {{ $regionLabel }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($app->status === 'pending')
                                    <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-300">
                                        {{ __('Pending') }}
                                    </span>
                                @elseif($app->status === 'approved')
                                    <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        {{ __('Approved') }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-300">
                                        {{ __('Rejected') }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    <button type="button"
                                        wire:click="showDetail({{ $app->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-outline-variant/50 bg-surface-container-lowest hover:bg-surface-container-low text-oceanic-deep font-label-bold text-xs transition-colors">
                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                        <span>{{ __('Details') }}</span>
                                    </button>

                                    @if($app->status === 'pending')
                                        <button type="button"
                                            wire:click="approve({{ $app->id }})"
                                            wire:confirm="{{ __('Approve this partner application and activate their Agent Portal?') }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-label-bold text-xs transition-colors">
                                            <span class="material-symbols-outlined text-sm">check</span>
                                            <span>{{ __('Approve') }}</span>
                                        </button>

                                        <button type="button"
                                            wire:click="openReject({{ $app->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-label-bold text-xs transition-colors">
                                            <span class="material-symbols-outlined text-sm">close</span>
                                            <span>{{ __('Reject') }}</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-on-surface-variant">
                                <span class="material-symbols-outlined text-4xl mb-2 text-outline/50">storefront</span>
                                <p class="font-label-bold">{{ __('No partner applications found.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-outline-variant/30 bg-surface-container-lowest">
            {{ $applications->links() }}
        </div>
    </div>

    <!-- Detail Modal -->
    @if($detailApplication)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4" wire:click.self="closeDetail">
            <div class="bg-surface-white rounded-3xl border border-outline-variant/30 shadow-2xl max-w-xl w-full overflow-hidden">
                <div class="px-6 py-5 border-b border-outline-variant/30 flex items-center justify-between gap-4">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-outline">{{ __('Partner Application Details') }}</span>
                        <h3 class="text-lg font-bold text-oceanic-deep">{{ $detailApplication->business_name }}</h3>
                    </div>
                    <button type="button" wire:click="closeDetail" class="p-1.5 rounded-lg text-outline hover:text-oceanic-deep hover:bg-surface-container-low">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="p-6 space-y-4 text-sm max-h-[75vh] overflow-y-auto">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="p-3.5 rounded-xl border border-outline-variant/25">
                            <span class="text-[11px] uppercase tracking-wider text-outline block mb-0.5">{{ __('Applicant') }}</span>
                            <div class="font-bold text-on-surface">{{ $detailApplication->user->name ?? '-' }}</div>
                            <div class="text-xs text-outline">{{ $detailApplication->user->email ?? '-' }}</div>
                        </div>

                        <div class="p-3.5 rounded-xl border border-outline-variant/25">
                            <span class="text-[11px] uppercase tracking-wider text-outline block mb-0.5">{{ __('WhatsApp Number') }}</span>
                            <div class="font-bold text-on-surface">{{ $detailApplication->whatsapp_number }}</div>
                        </div>

                        <div class="p-3.5 rounded-xl border border-outline-variant/25">
                            <span class="text-[11px] uppercase tracking-wider text-outline block mb-0.5">{{ __('Business License / NIB') }}</span>
                            <div class="font-mono font-bold text-on-surface">{{ $detailApplication->nib_number }}</div>
                        </div>

                        <div class="p-3.5 rounded-xl border border-outline-variant/25">
                            <span class="text-[11px] uppercase tracking-wider text-outline block mb-0.5">{{ __('Bali Operating Regions') }}</span>
                            <div class="font-semibold text-on-surface">{{ implode(', ', $detailApplication->regionLabels()) }}</div>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-surface-container-lowest border border-outline-variant/25 space-y-1">
                        <span class="text-[11px] uppercase tracking-wider text-outline font-semibold block">{{ __('Bank Account Details') }}</span>
                        <div class="font-bold text-oceanic-deep">{{ $detailApplication->bank_name }} — {{ $detailApplication->bank_account_number }}</div>
                        <div class="text-xs text-on-surface-variant">{{ __('Account Holder Name') }}: {{ $detailApplication->bank_account_name }}</div>
                    </div>

                    @if($detailApplication->status === 'rejected' && $detailApplication->rejection_reason)
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900">
                            <div class="text-xs font-bold uppercase tracking-wider text-rose-700 mb-1">{{ __('Rejection Reason') }}</div>
                            <p class="text-sm">{{ $detailApplication->rejection_reason }}</p>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 border-t border-outline-variant/30 bg-surface-container-lowest flex flex-wrap items-center justify-end gap-2.5">
                    @if($detailApplication->status === 'pending')
                        <button type="button" wire:click="openReject({{ $detailApplication->id }})"
                            class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-label-bold text-xs">
                            {{ __('Reject') }}
                        </button>
                        <button type="button" wire:click="approve({{ $detailApplication->id }})"
                            wire:confirm="{{ __('Approve this partner application and activate their Agent Portal?') }}"
                            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-label-bold text-xs">
                            {{ __('Approve') }}
                        </button>
                    @endif
                    <button type="button" wire:click="closeDetail"
                        class="px-4 py-2 rounded-xl bg-oceanic-deep hover:bg-primary text-white font-label-bold text-xs">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Reject Modal (Mandatory Reason) -->
    @if($rejectApplication)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4" wire:click.self="closeReject">
            <div class="bg-surface-white rounded-3xl border border-outline-variant/30 shadow-2xl max-w-lg w-full overflow-hidden">
                <div class="px-6 py-5 border-b border-outline-variant/30 flex items-center justify-between gap-4">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-rose-600">{{ __('Reject Partner Application') }}</span>
                        <h3 class="text-lg font-bold text-oceanic-deep">{{ $rejectApplication->business_name }}</h3>
                    </div>
                    <button type="button" wire:click="closeReject" class="p-1.5 rounded-lg text-outline hover:text-oceanic-deep hover:bg-surface-container-low">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="reject">
                    <div class="p-6 space-y-3">
                        <label for="rejectionReason" class="block font-label-bold text-sm text-oceanic-deep">
                            {{ __('Rejection Reason') }} <span class="text-error">*</span>
                        </label>
                        <textarea id="rejectionReason"
                            wire:model="rejectionReason"
                            rows="4"
                            required
                            placeholder="{{ __('Explain clearly why this application is rejected (shown to the applicant)...') }}"
                            class="w-full bg-surface-container-low border border-outline-variant/50 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/40 focus:border-rose-500"></textarea>
                        @error('rejectionReason')
                            <span class="text-error text-xs block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="px-6 py-4 border-t border-outline-variant/30 bg-surface-container-lowest flex justify-end gap-2.5">
                        <button type="button" wire:click="closeReject"
                            class="px-4 py-2 rounded-xl border border-outline-variant/50 text-on-surface-variant hover:bg-surface-container-low font-label-bold text-xs">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit"
                            class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-label-bold text-xs">
                            {{ __('Confirm Rejection') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

