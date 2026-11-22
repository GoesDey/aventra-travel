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
            'preferred_regions.*'    => ['string', 'in:utara,selatan,timur,barat'],
            'budget_max'             => ['required', 'numeric', 'min:0', 'max:9999999999'],
        ], [
            'preferred_categories.required' => __('Please select at least one package type.'),
            'preferred_regions.required'    => __('Please select at least one Bali region.'),
            'budget_max.required'           => __('Please enter your maximum budget per person.'),
            'budget_max.numeric'            => __('Maximum budget must be a valid number.'),
            'budget_max.min'                => __('Maximum budget cannot be negative.'),
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
            ->with('success', __('Your travel preferences have been saved! Matching tour packages are now shown first.'));
    }
}

