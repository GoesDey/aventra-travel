<div>
    <!-- Actions Bar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div class="flex items-center gap-4 w-full md:w-auto">
            <div class="relative w-full md:w-80">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
                <input wire:model.live="search" class="w-full bg-surface-white border border-outline-variant/50 rounded-lg pl-10 pr-4 py-2 text-body-md font-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-tropical-teal/50 focus:border-tropical-teal transition-all placeholder:text-outline/70 shadow-sm" placeholder="Search packages..." type="text">
            </div>
            <div class="hidden md:flex items-center gap-2">
                <span class="font-caption text-[11px] uppercase tracking-wider text-outline">Total Packages:</span>
                <span class="font-label-bold text-sm text-oceanic-deep">{{ $packages->total() }}</span>
            </div>
        </div>
        <button wire:click="openCreate" class="w-full md:w-auto bg-tropical-teal text-surface-white font-label-bold text-label-bold px-6 py-2.5 rounded-full flex items-center justify-center gap-2 hover:bg-oceanic-deep transition-all shadow-[0_4px_10px_rgba(0,35,102,0.1)] hover:shadow-[0_10px_30px_rgba(0,35,102,0.15)]">
            <span class="material-symbols-outlined text-[20px]">add</span>
            New Package
        </button>
    </div>

    @if (session()->has('success'))
        <div class="bg-primary-container text-on-primary-container p-4 rounded-xl mb-6 font-label-bold text-sm shadow-sm border border-primary-container/20">
            {{ session('success') }}
        </div>
    @endif

    <!-- Data Table Container -->
    <div class="bg-surface-white rounded-2xl border border-outline-variant/30 shadow-[0_10px_30px_rgba(0,35,102,0.05)] overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-surface-container-lowest border-b border-outline-variant/30 text-on-surface-variant">
                        <th class="py-4 px-6 font-label-bold text-label-bold w-24">ID</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold">Package Details</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold text-center">Duration</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold text-right">Price/Pax</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold text-center w-32">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 text-body-md font-body-md">
                    @forelse($packages as $package)
                    <tr class="hover:bg-surface-container-low/50 transition-colors group">
                        <td class="py-4 px-6 text-outline font-medium">#PKG-{{ $package->id }}</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-4">
                                @if($package->cover_image)
                                    <img src="{{ asset('storage/' . $package->cover_image) }}" class="w-14 h-14 rounded-lg object-cover shadow-sm border border-outline-variant/20">
                                @else
                                    <div class="w-14 h-14 rounded-lg bg-surface-container-low border border-outline-variant/30 flex items-center justify-center text-outline/50">
                                        <span class="material-symbols-outlined text-2xl">tour</span>
                                    </div>
                                @endif
                                <div>
                                    <div class="font-label-bold text-sm text-oceanic-deep mb-0.5 flex items-center gap-2">
                                        {{ $package->name }}
                                        @if(!$package->is_active)
                                            <span class="px-2 py-0.5 bg-surface-variant text-on-surface-variant text-[10px] rounded uppercase font-bold tracking-wider">Inactive</span>
                                        @endif
                                    </div>
                                    <div class="font-caption text-[11px] text-on-surface-variant flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">location_on</span>
                                        {{ count($package->destinations) }} Destinations
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-center text-on-surface font-medium">{{ $package->duration_days }} Days</td>
                        <td class="py-4 px-6 text-right font-label-bold text-tropical-teal">Rp {{ number_format($package->price_per_pax, 0, ',', '.') }}</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center justify-center gap-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                                <button wire:click="openEdit({{ $package->id }})" class="p-1.5 text-on-surface-variant hover:text-tropical-teal hover:bg-surface-container-low rounded-md transition-colors" title="Edit">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </button>
                                <button wire:click="delete({{ $package->id }})" wire:confirm="Delete package?" class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container/30 rounded-md transition-colors" title="Delete">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl mb-2 text-outline/50">tour</span>
                            <p class="font-label-bold">No packages found.</p>
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

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div wire:click="$set('showModal', false)" class="absolute inset-0 bg-oceanic-deep/40 backdrop-blur-sm transition-opacity"></div>
        <div class="bg-surface-white w-full max-w-5xl rounded-2xl shadow-[0_20px_60px_rgba(0,35,102,0.15)] relative z-10 flex flex-col max-h-[90vh] border border-outline-variant/30">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-outline-variant/30 bg-surface-white rounded-t-2xl shrink-0">
                <h2 class="font-headline-md text-xl font-semibold text-oceanic-deep">{{ $editing ? 'Edit Package' : 'Create New Package' }}</h2>
                <button wire:click="$set('showModal', false)" class="p-1.5 text-outline hover:text-error hover:bg-error-container/20 rounded-full transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            
            <form wire:submit.prevent="save" class="flex-1 overflow-hidden flex flex-col">
                <div class="p-6 overflow-y-auto flex-1 grid grid-cols-1 lg:grid-cols-5 gap-8">
                    <!-- Left Column (Form Fields) -->
                    <div class="lg:col-span-3 space-y-5">
                        <div>
                            <label class="block font-label-bold text-sm text-oceanic-deep mb-1.5">Package Name</label>
                            <input wire:model="name" type="text" class="w-full bg-surface-container-low border border-outline-variant/50 rounded-xl px-4 py-3 text-body-md focus:outline-none focus:ring-1 focus:ring-tropical-teal focus:border-tropical-teal transition-all" placeholder="e.g. 3 Days Bali Escapade" required>
                            @error('name') <span class="text-error font-caption text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block font-label-bold text-sm text-oceanic-deep mb-1.5">Description</label>
                            <textarea wire:model="description" class="w-full bg-surface-container-low border border-outline-variant/50 rounded-xl px-4 py-3 text-body-md focus:outline-none focus:ring-1 focus:ring-tropical-teal focus:border-tropical-teal transition-all resize-none" rows="3" placeholder="Describe the travel package..." required></textarea>
                            @error('description') <span class="text-error font-caption text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-label-bold text-[13px] text-oceanic-deep mb-1.5">Price/Pax (Rp)</label>
                                <input wire:model="price_per_pax" type="number" min="0" class="w-full bg-surface-container-low border border-outline-variant/50 rounded-xl px-4 py-2.5 text-body-md focus:outline-none focus:ring-1 focus:ring-tropical-teal transition-all" placeholder="500000" required>
                                @error('price_per_pax') <span class="text-error font-caption text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block font-label-bold text-[13px] text-oceanic-deep mb-1.5">Duration (Days)</label>
                                <input wire:model="duration_days" type="number" min="1" class="w-full bg-surface-container-low border border-outline-variant/50 rounded-xl px-4 py-2.5 text-body-md focus:outline-none focus:ring-1 focus:ring-tropical-teal transition-all" placeholder="3" required>
                                @error('duration_days') <span class="text-error font-caption text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block font-label-bold text-[13px] text-oceanic-deep mb-1.5">Max Guests</label>
                                <input wire:model="max_guests" type="number" min="1" class="w-full bg-surface-container-low border border-outline-variant/50 rounded-xl px-4 py-2.5 text-body-md focus:outline-none focus:ring-1 focus:ring-tropical-teal transition-all" placeholder="10" required>
                                @error('max_guests') <span class="text-error font-caption text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/50">
                            <div>
                                <p class="font-label-bold text-sm text-oceanic-deep">Active Status</p>
                                <p class="font-caption text-[11px] text-on-surface-variant mt-0.5">Inactive packages won't appear on the customer site.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input wire:model="is_active" type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-outline-variant/40 peer-focus:ring-2 peer-focus:ring-tropical-teal/50 rounded-full peer peer-checked:bg-tropical-teal after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-surface-white after:border-outline-variant/30 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5 peer-checked:after:border-white shadow-inner"></div>
                            </label>
                        </div>
                        
                        <div>
                            <label class="block font-label-bold text-sm text-oceanic-deep mb-1.5">Cover Image</label>
                            <input wire:model="image" type="file" accept="image/*" class="block w-full text-sm text-outline file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-label-bold file:bg-surface-container-low file:text-oceanic-deep hover:file:bg-surface-container transition-all cursor-pointer">
                            @error('image') <span class="text-error font-caption text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <!-- Right Column (Destinations Picker) -->
                    <div class="lg:col-span-2 flex flex-col h-full bg-surface-container-lowest border border-outline-variant/40 rounded-xl overflow-hidden shadow-inner">
                        <div class="p-4 border-b border-outline-variant/40 bg-surface-white">
                            <label class="block font-label-bold text-sm text-oceanic-deep">Package Itinerary</label>
                            <p class="font-caption text-[11px] text-on-surface-variant mt-0.5">Select destinations included in this package.</p>
                        </div>
                        <div class="p-2 overflow-y-auto flex-1 h-64 lg:h-auto">
                            <div class="space-y-1">
                                @foreach($availableDestinations as $dest)
                                <label class="flex items-start gap-3 p-3 hover:bg-surface-white rounded-lg cursor-pointer transition-colors border border-transparent hover:border-outline-variant/30 hover:shadow-sm">
                                    <div class="mt-0.5 flex-shrink-0">
                                        <input wire:model="selectedDestinations" type="checkbox" value="{{ $dest->id }}" class="w-4 h-4 rounded border-outline-variant text-tropical-teal focus:ring-tropical-teal focus:ring-1">
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-label-bold text-sm text-oceanic-deep truncate">{{ $dest->name }}</p>
                                        <p class="font-caption text-[11px] text-outline truncate flex items-center gap-1 mt-0.5">
                                            <span class="material-symbols-outlined text-[12px]">location_on</span>
                                            {{ $dest->location }}
                                        </p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 border-t border-outline-variant/30 bg-surface-container-lowest rounded-b-2xl flex justify-end gap-3 shrink-0">
                    <button type="button" wire:click="$set('showModal', false)" class="px-6 py-2.5 rounded-full border border-outline-variant text-on-surface-variant hover:bg-surface-container-low hover:text-oceanic-deep font-label-bold text-sm transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-full bg-tropical-teal hover:bg-oceanic-deep text-surface-white font-label-bold text-sm transition-colors shadow-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Save Package
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
