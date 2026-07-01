<div>
    <!-- Actions Bar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div class="flex items-center gap-4 w-full md:w-auto">
            <div class="relative w-full md:w-80">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
                <input wire:model.live="search" class="w-full bg-surface-white border border-outline-variant/50 rounded-lg pl-10 pr-4 py-2 text-body-md font-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-tropical-teal/50 focus:border-tropical-teal transition-all placeholder:text-outline/70 shadow-sm" placeholder="Search destinations..." type="text">
            </div>
            @if($search)
            <div class="hidden md:flex items-center gap-2">
                <span class="font-caption text-[11px] uppercase tracking-wider text-outline">Showing results for:</span>
                <span class="font-label-bold text-sm text-oceanic-deep">{{ $search }}</span>
            </div>
            @endif
        </div>
        <button wire:click="openCreate" class="w-full md:w-auto bg-tropical-teal text-surface-white font-label-bold text-label-bold px-6 py-2.5 rounded-full flex items-center justify-center gap-2 hover:bg-oceanic-deep transition-all shadow-[0_4px_10px_rgba(0,35,102,0.1)] hover:shadow-[0_10px_30px_rgba(0,35,102,0.15)]">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Add New Destination
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
                        <th class="py-4 px-6 font-label-bold text-label-bold">Destination Details</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold">Location</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold w-40">Date Added</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold w-32 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 text-body-md font-body-md">
                    @forelse($destinations as $destination)
                    <tr class="hover:bg-surface-container-low/50 transition-colors group">
                        <td class="py-4 px-6 text-outline font-medium">#DST-{{ $destination->id }}</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-4">
                                @if($destination->image_path)
                                    <img src="{{ asset('storage/' . $destination->image_path) }}" class="w-14 h-14 rounded-lg object-cover shadow-sm border border-outline-variant/20">
                                @else
                                    <div class="w-14 h-14 rounded-lg bg-surface-container-low border border-outline-variant/30 flex items-center justify-center text-outline/50">
                                        <span class="material-symbols-outlined text-2xl">image</span>
                                    </div>
                                @endif
                                <div>
                                    <div class="font-label-bold text-sm text-oceanic-deep mb-0.5">{{ $destination->name }}</div>
                                    <div class="font-caption text-[11px] text-on-surface-variant line-clamp-1">{{ Str::limit($destination->description, 50) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-on-surface font-medium">{{ $destination->location }}</td>
                        <td class="py-4 px-6 text-outline font-caption text-[11px]">{{ $destination->created_at->format('M d, Y') }}</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center justify-center gap-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                                <button wire:click="openEdit({{ $destination->id }})" class="p-1.5 text-on-surface-variant hover:text-tropical-teal hover:bg-surface-container-low rounded-md transition-colors" title="Edit">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </button>
                                <button wire:click="delete({{ $destination->id }})" wire:confirm="Yakin ingin menghapus destinasi ini?" class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container/30 rounded-md transition-colors" title="Delete">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl mb-2 text-outline/50">map</span>
                            <p class="font-label-bold">No destinations found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-outline-variant/30 bg-surface-container-lowest">
            {{ $destinations->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div wire:click="$set('showModal', false)" class="absolute inset-0 bg-oceanic-deep/40 backdrop-blur-sm transition-opacity"></div>
        
        <!-- Modal Content -->
        <div class="bg-surface-white w-full max-w-2xl rounded-2xl shadow-[0_20px_60px_rgba(0,35,102,0.15)] relative z-10 flex flex-col max-h-[90vh] border border-outline-variant/30">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-outline-variant/30 bg-surface-white rounded-t-2xl">
                <h2 class="font-headline-md text-xl font-semibold text-oceanic-deep">{{ $editing ? 'Edit Destination' : 'Add New Destination' }}</h2>
                <button wire:click="$set('showModal', false)" class="p-1.5 text-outline hover:text-error hover:bg-error-container/20 rounded-full transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            
            <!-- Body -->
            <form wire:submit.prevent="save" class="flex flex-col flex-1 overflow-hidden">
                <div class="p-6 overflow-y-auto flex-1 space-y-5">
                    <div>
                        <label class="block font-label-bold text-sm text-oceanic-deep mb-1.5">Destination Name</label>
                        <input wire:model="name" class="w-full bg-surface-container-low border border-outline-variant/50 rounded-xl px-4 py-3 text-body-md focus:outline-none focus:ring-1 focus:ring-tropical-teal focus:border-tropical-teal transition-all" type="text" placeholder="e.g. Bali, Kyoto, etc." required>
                        @error('name') <span class="text-error font-caption text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-label-bold text-sm text-oceanic-deep mb-1.5">Location / Region</label>
                        <input wire:model="location" class="w-full bg-surface-container-low border border-outline-variant/50 rounded-xl px-4 py-3 text-body-md focus:outline-none focus:ring-1 focus:ring-tropical-teal focus:border-tropical-teal transition-all" type="text" placeholder="e.g. Indonesia, Japan" required>
                        @error('location') <span class="text-error font-caption text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-label-bold text-sm text-oceanic-deep mb-1.5">Description</label>
                        <textarea wire:model="description" class="w-full bg-surface-container-low border border-outline-variant/50 rounded-xl px-4 py-3 text-body-md focus:outline-none focus:ring-1 focus:ring-tropical-teal focus:border-tropical-teal transition-all resize-none" rows="4" placeholder="Briefly describe what makes this destination special..." required></textarea>
                        @error('description') <span class="text-error font-caption text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-label-bold text-sm text-oceanic-deep mb-1.5">Destination Image</label>
                        <input wire:model="image" type="file" class="block w-full text-sm text-outline file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-label-bold file:bg-surface-container-low file:text-oceanic-deep hover:file:bg-surface-container transition-all cursor-pointer">
                        @error('image') <span class="text-error font-caption text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="p-6 border-t border-outline-variant/30 bg-surface-container-lowest rounded-b-2xl flex justify-end gap-3">
                    <button type="button" wire:click="$set('showModal', false)" class="px-6 py-2.5 rounded-full border border-outline-variant text-on-surface-variant hover:bg-surface-container-low hover:text-oceanic-deep font-label-bold text-sm transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-full bg-tropical-teal hover:bg-oceanic-deep text-surface-white font-label-bold text-sm transition-colors shadow-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Save Destination
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
