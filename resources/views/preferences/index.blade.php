<x-layouts.app title="Minat Wisata Anda - TriHitaTour">
    @php
        $cats = [
            'reguler' => ['label' => 'Reguler', 'desc' => 'Open trip bersama wisatawan lain, hemat & seru', 'icon' => 'groups'],
            'custom'  => ['label' => 'Custom',  'desc' => 'Private trip, itinerary sesuai keinginan Anda', 'icon' => 'tune'],
        ];
        $regs = [
            'timur'   => ['label' => 'Bali Timur',  'icon' => 'landscape'],
            'barat'   => ['label' => 'Bali Barat',  'icon' => 'sunny'],
            'lainnya' => ['label' => 'Lainnya',     'icon' => 'explore'],
        ];
        $oldCats = old('preferred_categories', $selectedCategories);
        $oldRegs = old('preferred_regions', $selectedRegions);
        $oldBudget = old('budget_max', $budgetMax !== null ? (int) $budgetMax : '');
    @endphp

    <div class="bg-surface-container-lowest min-h-screen pt-28 pb-16 px-4">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-10 space-y-3">
                <div class="inline-flex items-center gap-2 bg-tropical-teal/10 text-tropical-teal px-4 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider">
                    <span class="material-symbols-outlined text-sm">auto_awesome</span>
                    <span>Rekomendasi Personal</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-oceanic-deep">Ceritakan Minat Wisata Anda</h1>
                <p class="text-sm text-on-surface-variant max-w-xl mx-auto">
                    Kami akan menampilkan paket yang paling sesuai dengan selera dan budget Anda di urutan teratas.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-xl bg-red-50 border border-red-200 text-red-700 p-4 text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('preferences.store') }}" method="POST"
                  class="bg-surface-white rounded-3xl border border-outline-variant/30 shadow-sm p-6 md:p-10 space-y-10">
                @csrf

                {{-- Kategori --}}
                <section class="space-y-4">
                    <h2 class="text-lg font-bold text-oceanic-deep">1. Jenis Paket yang Diminati</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($cats as $value => $c)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="preferred_categories[]" value="{{ $value }}"
                                       class="peer sr-only" @checked(in_array($value, $oldCats))>
                                <div class="h-full rounded-2xl border-2 border-outline-variant/40 p-5 transition-all
                                            hover:border-tropical-teal/60
                                            peer-checked:border-tropical-teal peer-checked:bg-tropical-teal/10 peer-checked:shadow-md
                                            peer-focus-visible:ring-2 peer-focus-visible:ring-tropical-teal">
                                    <span class="material-symbols-outlined text-tropical-teal text-3xl">{{ $c['icon'] }}</span>
                                    <div class="mt-2 font-bold text-oceanic-deep">{{ $c['label'] }}</div>
                                    <p class="text-xs text-on-surface-variant mt-1">{{ $c['desc'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </section>

                {{-- Wilayah --}}
                <section class="space-y-4">
                    <h2 class="text-lg font-bold text-oceanic-deep">2. Wilayah Tujuan</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach ($regs as $value => $r)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="preferred_regions[]" value="{{ $value }}"
                                       class="peer sr-only" @checked(in_array($value, $oldRegs))>
                                <div class="rounded-2xl border-2 border-outline-variant/40 p-5 text-center transition-all
                                            hover:border-tropical-teal/60
                                            peer-checked:border-tropical-teal peer-checked:bg-tropical-teal/10 peer-checked:shadow-md
                                            peer-focus-visible:ring-2 peer-focus-visible:ring-tropical-teal">
                                    <span class="material-symbols-outlined text-tropical-teal text-3xl">{{ $r['icon'] }}</span>
                                    <div class="mt-2 font-bold text-oceanic-deep">{{ $r['label'] }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </section>

                {{-- Budget --}}
                <section class="space-y-4">
                    <h2 class="text-lg font-bold text-oceanic-deep">3. Maksimal Budget per Orang</h2>
                    <div class="flex items-center rounded-2xl border-2 border-outline-variant/40 focus-within:border-tropical-teal overflow-hidden">
                        <span class="px-5 py-3 bg-surface-container-low text-on-surface-variant font-semibold">Rp</span>
                        <input type="number" name="budget_max" min="0" step="10000" required
                               value="{{ $oldBudget }}" placeholder="contoh: 750000"
                               class="w-full px-4 py-3 bg-transparent border-none focus:outline-none focus:ring-0 text-on-surface">
                    </div>
                </section>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-between gap-3 pt-2">
                    <a href="{{ route('packages.index') }}" class="text-center text-sm text-outline hover:text-oceanic-deep px-6 py-3">
                        Lewati untuk sekarang
                    </a>
                    <button type="submit"
                            class="bg-tropical-teal hover:bg-secondary text-white font-bold px-8 py-3 rounded-full shadow transition-colors">
                        Simpan & Lihat Rekomendasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

