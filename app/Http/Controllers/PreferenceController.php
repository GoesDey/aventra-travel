<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    /**
     * Tampilkan kuesioner minat wisatawan (terisi otomatis jika sudah pernah mengisi).
     */
    public function index(Request $request)
    {
        $preference = $request->user()->userPreference;

        return view('preferences.index', [
            'selectedCategories' => $preference?->preferred_categories ?? [],
            'selectedRegions'    => $preference?->preferred_regions ?? [],
            'budgetMax'          => $preference?->budget_max,
        ]);
    }

    /**
     * Simpan / perbarui preferensi wisatawan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'preferred_categories'   => ['required', 'array', 'min:1'],
            'preferred_categories.*' => ['string', 'in:reguler,custom'],
            'preferred_regions'      => ['required', 'array', 'min:1'],
            'preferred_regions.*'    => ['string', 'in:timur,barat,lainnya'],
            'budget_max'             => ['required', 'numeric', 'min:0'],
        ], [
            'preferred_categories.required' => 'Pilih minimal satu kategori paket.',
            'preferred_regions.required'    => 'Pilih minimal satu wilayah.',
            'budget_max.required'           => 'Masukkan budget maksimal per orang.',
        ]);

        $request->user()->userPreference()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'preferred_categories' => array_values($validated['preferred_categories']),
                'preferred_regions'    => array_values($validated['preferred_regions']),
                'budget_max'           => $validated['budget_max'],
            ]
        );

        return redirect()->route('packages.index')
            ->with('success', 'Preferensi wisata Anda berhasil disimpan! Paket yang cocok kini ditampilkan lebih dulu.');
    }
}

