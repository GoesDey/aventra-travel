<div>
    <!-- Actions Bar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
            <div class="relative w-full md:w-80">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
                <input wire:model.live="search" class="w-full bg-surface-white border border-outline-variant/50 rounded-lg pl-10 pr-4 py-2 text-body-md font-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-tropical-teal/50 focus:border-tropical-teal transition-all placeholder:text-outline/70 shadow-sm" placeholder="{{ __('Search packages...') }}" type="text">
            </div>
            <div class="hidden md:flex items-center gap-2">
                <span class="font-caption text-[11px] uppercase tracking-wider text-outline">{{ __('Total Packages:') }}</span>
                <span class="font-label-bold text-sm text-oceanic-deep">{{ $packages->total() }}</span>
            </div>
        </div>
        <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-300">
            {{ __('Moderation Mode (View & Toggle Active)') }}
        </span>
    </div>

    @if (session()->has('success'))
        <div class="bg-primary-container text-on-primary-container p-4 rounded-xl mb-6 font-label-bold text-sm shadow-sm border border-primary-container/20">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-error-container text-on-error-container p-4 rounded-xl mb-6 font-label-bold text-sm shadow-sm border border-error-container/20">
            {{ session('error') }}
        </div>
    @endif

    <!-- Data Table Container -->
    <div class="bg-surface-white rounded-2xl border border-outline-variant/30 shadow-[0_10px_30px_rgba(0,35,102,0.05)] overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[860px]">
                <thead>
                    <tr class="bg-surface-container-lowest border-b border-outline-variant/30 text-on-surface-variant">
                        <th class="py-4 px-5 font-label-bold text-label-bold w-24">ID</th>
                        <th class="py-4 px-5 font-label-bold text-label-bold">{{ __('Package Details') }}</th>
                        <th class="py-4 px-5 font-label-bold text-label-bold text-center">{{ __('Duration') }}</th>
                        <th class="py-4 px-5 font-label-bold text-label-bold text-right">{{ __('Price per Person') }}</th>
                        <th class="py-4 px-5 font-label-bold text-label-bold text-center">{{ __('Status') }}</th>
                        <th class="py-4 px-5 font-label-bold text-label-bold text-center w-36">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 text-body-md font-body-md">
                    @forelse($packages as $package)
                        <tr class="hover:bg-surface-container-low/50 transition-colors group">
                            <td class="py-4 px-5 text-outline font-medium">#PKG-{{ $package->id }}</td>
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-4">
                                    @if($package->cover_image_url)
                                        <img src="{{ $package->cover_image_url }}" alt="{{ $package->name }}" class="w-14 h-14 rounded-lg object-cover shadow-sm border border-outline-variant/20 shrink-0">
                                    @else
                                        <div class="w-14 h-14 rounded-lg bg-surface-container-low border border-outline-variant/30 flex items-center justify-center text-outline/50 shrink-0">
                                            <span class="material-symbols-outlined text-2xl">tour</span>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="font-label-bold text-sm text-oceanic-deep mb-0.5 truncate">{{ $package->name }}</div>
                                        <div class="font-caption text-[11px] text-outline flex flex-wrap items-center gap-2">
                                            <span>{{ $package->agent->agent_name ?? '-' }}</span>
                                            <span>•</span>
                                            <span>{{ $package->destinations->count() }} {{ __('Destinations') }}</span>
                                            <span>•</span>
                                            <span>{{ $package->schedules_count }} {{ __('Schedules') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-5 text-center text-on-surface font-medium">
                                {{ $package->duration_days }} {{ $package->duration_days > 1 ? __('Days') : __('Day') }}
                            </td>
                            <td class="py-4 px-5 text-right font-label-bold text-tropical-teal">
                                Rp {{ number_format($package->price_per_pax, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5 text-center">
                                <button type="button"
                                    wire:click="toggleActive({{ $package->id }})"
                                    title="{{ __('Toggle active status') }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider border transition-colors cursor-pointer {{ $package->is_active ? 'bg-emerald-100 text-emerald-800 border-emerald-300 hover:bg-emerald-200/70' : 'bg-slate-100 text-slate-700 border-slate-300 hover:bg-slate-200/70' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $package->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    <span>{{ $package->is_active ? __('Active') : __('Inactive') }}</span>
                                </button>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <button type="button"
                                    wire:click="showDetail({{ $package->id }})"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg border border-outline-variant/50 bg-surface-container-lowest hover:bg-surface-container-low text-oceanic-deep font-label-bold text-xs transition-colors">
                                    <span class="material-symbols-outlined text-sm">visibility</span>
                                    <span>{{ __('Details') }}</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-on-surface-variant">
                                <span class="material-symbols-outlined text-4xl mb-2 text-outline/50">tour</span>
                                <p class="font-label-bold">{{ __('No packages found.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-outline-variant/30 bg-surface-container-lowest">
            {{ $packages->links() }}
        </div>
    </div>

    <!-- Read-Only Package Detail Modal -->
    @if($detailPackage)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4" wire:click.self="closeDetail">
            <div class="bg-surface-white rounded-3xl border border-outline-variant/30 shadow-2xl max-w-2xl w-full overflow-hidden">
                <div class="px-6 py-5 border-b border-outline-variant/30 flex items-center justify-between gap-4">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-outline">{{ __('Package Details') }} ({{ __('Read-only') }})</span>
                        <h3 class="text-lg font-bold text-oceanic-deep">{{ $detailPackage->name }}</h3>
                    </div>
                    <button type="button" wire:click="closeDetail" class="p-1.5 rounded-lg text-outline hover:text-oceanic-deep hover:bg-surface-container-low">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="p-6 space-y-4 text-sm max-h-[75vh] overflow-y-auto">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="p-3.5 rounded-xl border border-outline-variant/25">
                            <span class="text-[11px] uppercase tracking-wider text-outline block mb-0.5">{{ __('Tour Operator') }}</span>
                            <div class="font-bold text-on-surface">{{ $detailPackage->agent->agent_name ?? '-' }}</div>
                            <div class="text-xs text-outline">{{ $detailPackage->agent->user->email ?? '-' }}</div>
                        </div>

                        <div class="p-3.5 rounded-xl border border-outline-variant/25">
                            <span class="text-[11px] uppercase tracking-wider text-outline block mb-0.5">{{ __('Price per Person') }}</span>
                            <div class="font-bold text-tropical-teal">Rp {{ number_format($detailPackage->price_per_pax, 0, ',', '.') }}</div>
                            <div class="text-xs text-outline">{{ $detailPackage->duration_days }} {{ $detailPackage->duration_days > 1 ? __('Days') : __('Day') }} • {{ __('Max.') }} {{ $detailPackage->max_guests }} {{ __('Pax') }}</div>
                        </div>

                        <div class="p-3.5 rounded-xl border border-outline-variant/25">
                            <span class="text-[11px] uppercase tracking-wider text-outline block mb-0.5">{{ __('Status') }}</span>
                            <div class="font-bold text-on-surface">{{ $detailPackage->is_active ? __('Active') : __('Inactive') }}</div>
                            <div class="text-xs text-outline">{{ $detailPackage->region_label }} • {{ $detailPackage->category === 'custom' ? __('Custom') : __('Regular') }}</div>
                        </div>

                        <div class="p-3.5 rounded-xl border border-outline-variant/25">
                            <span class="text-[11px] uppercase tracking-wider text-outline block mb-0.5">{{ __('Bookings') }}</span>
                            <div class="font-bold text-on-surface">{{ $detailPackage->bookings_count }} {{ __('Bookings') }}</div>
                            <div class="text-xs text-outline">{{ $detailPackage->schedules->count() }} {{ __('Schedules') }}</div>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-surface-container-lowest border border-outline-variant/25 space-y-1">
                        <span class="text-[11px] uppercase tracking-wider text-outline font-semibold block">{{ __('About This Package') }}</span>
                        <p class="text-xs text-on-surface-variant leading-relaxed whitespace-pre-line">{{ $detailPackage->description }}</p>
                    </div>

                    @if($detailPackage->destinations->isNotEmpty())
                        <div class="p-4 rounded-2xl bg-surface-container-lowest border border-outline-variant/25 space-y-2">
                            <span class="text-[11px] uppercase tracking-wider text-outline font-semibold block">{{ __('Destinations Visited') }}</span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($detailPackage->destinations as $dest)
                                    <span class="px-2.5 py-1 rounded-lg bg-surface-container-low text-xs font-semibold text-oceanic-deep border border-outline-variant/30">
                                        {{ $dest->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 border-t border-outline-variant/30 bg-surface-container-lowest flex justify-between items-center gap-3">
                    <button type="button" wire:click="toggleActive({{ $detailPackage->id }})"
                        class="px-4 py-2 rounded-xl border border-outline-variant/50 text-xs font-label-bold text-oceanic-deep hover:bg-surface-container-low">
                        {{ $detailPackage->is_active ? __('Deactivate Package') : __('Activate Package') }}
                    </button>
                    <button type="button" wire:click="closeDetail"
                        class="px-5 py-2 rounded-xl bg-oceanic-deep hover:bg-primary text-white font-label-bold text-xs transition-colors">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
