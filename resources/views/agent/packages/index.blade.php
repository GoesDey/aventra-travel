<x-layouts.agent title="Tour Package Management">
    <div class="space-y-6">
        
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-surface-white p-6 rounded-2xl border border-outline-variant/30 shadow-sm">
            <div>
                <h2 class="font-display-lg text-xl md:text-2xl font-bold text-oceanic-deep">Tour Package List</h2>
                <p class="text-sm text-on-surface-variant mt-1">Manage all your travel tour packages, schedules, and guest quotas.</p>
            </div>
            <div>
                <a href="{{ route('agent.packages.create') }}" class="inline-flex items-center gap-2 bg-tropical-teal hover:bg-secondary text-white font-label-bold text-sm px-5 py-3 rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                    <span class="material-symbols-outlined text-xl">add_circle</span>
                    <span>Add New Package</span>
                </a>
            </div>
        </div>

        <!-- Table Container -->
        <div class="bg-surface-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
            @if($packages->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-surface-container-low rounded-full flex items-center justify-center mx-auto mb-4 text-outline">
                        <span class="material-symbols-outlined text-4xl">inventory_2</span>
                    </div>
                    <h3 class="font-headline-md text-lg font-bold text-oceanic-deep mb-1">No Tour Packages Yet</h3>
                    <p class="text-sm text-on-surface-variant max-w-md mx-auto mb-6">
                        You haven't added any tour packages yet. Click the button below to start publishing your first package.
                    </p>
                    <a href="{{ route('agent.packages.create') }}" class="inline-flex items-center gap-2 bg-tropical-teal hover:bg-secondary text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow transition-colors">
                        <span class="material-symbols-outlined text-lg">add</span>
                        <span>Create First Package</span>
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-container-low border-b border-outline-variant/30 text-xs font-bold text-outline uppercase tracking-wider">
                                <th class="py-4 px-6">Tour Package</th>
                                <th class="py-4 px-4">Category & Region</th>
                                <th class="py-4 px-4">Price / Pax</th>
                                <th class="py-4 px-4">Duration & Guests</th>
                                <th class="py-4 px-4">Schedules</th>
                                <th class="py-4 px-4">Status</th>
                                <th class="py-4 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/20 text-sm">
                            @foreach($packages as $package)
                                <tr class="hover:bg-surface-container-lowest transition-colors">
                                    <!-- Name & Cover -->
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            @php
                                                $coverUrl = 'https://placehold.co/400x400/00327D/ffffff?text=' . urlencode($package->name);
                                                if ($package->cover_image) {
                                                    $coverUrl = str_starts_with($package->cover_image, 'http') ? $package->cover_image : asset('storage/' . $package->cover_image);
                                                }
                                            @endphp
                                            <img src="{{ $coverUrl }}" alt="{{ $package->name }}" class="w-14 h-14 rounded-xl object-cover border border-outline-variant/30 shrink-0">
                                            <div>
                                                <div class="font-bold text-oceanic-deep line-clamp-1">{{ $package->name }}</div>
                                                <div class="text-xs text-outline font-mono mt-0.5">/packages/{{ $package->slug }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Category & Region -->
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <div class="flex flex-col gap-1 items-start">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $package->category === 'custom' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ ucfirst($package->category) }}
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-900">
                                                {{ \App\Models\Package::regionLabel($package->region) }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Price -->
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <span class="font-bold text-oceanic-deep">Rp {{ number_format($package->price_per_pax, 0, ',', '.') }}</span>
                                    </td>

                                    <!-- Duration & Guests -->
                                    <td class="py-4 px-4 whitespace-nowrap text-on-surface-variant">
                                        <div><span class="font-semibold text-on-surface">{{ $package->duration_days }}</span> {{ Str::plural('Day', $package->duration_days) }}</div>
                                        <div class="text-xs text-outline">Max {{ $package->max_guests }} guests</div>
                                    </td>

                                    <!-- Schedule -->
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5 font-medium text-on-surface">
                                            <span class="material-symbols-outlined text-tropical-teal text-base">calendar_month</span>
                                            <span>{{ $package->schedules_count }} {{ Str::plural('Schedule', $package->schedules_count) }}</span>
                                        </div>
                                        @if($package->schedules->isNotEmpty())
                                            <div class="text-xs text-outline mt-0.5">
                                                Next: {{ $package->schedules->first()->start_date->format('d M Y') }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Status -->
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        @if($package->is_active)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="{{ route('agent.packages.edit', $package->id) }}" class="p-2 text-oceanic-deep hover:bg-surface-container rounded-lg transition-colors" title="Edit Package">
                                                <span class="material-symbols-outlined text-lg">edit</span>
                                            </a>

                                            <form action="{{ route('agent.packages.destroy', $package->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete &quot;{{ $package->name }}&quot; and all its schedules?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-error hover:bg-rose-50 rounded-lg transition-colors" title="Delete Package">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($packages->hasPages())
                    <div class="p-6 border-t border-outline-variant/30">
                        {{ $packages->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.agent>
