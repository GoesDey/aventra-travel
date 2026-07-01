<?php

namespace App\Livewire\Customer;

use App\Models\Package;
use Livewire\Component;
use Livewire\WithPagination;

class PackageList extends Component
{
    use WithPagination;

    public $search = '';
    public $minPrice = 0;
    public $maxPrice = 5000000;
    public $minRating = 0;
    public $durationFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingMinPrice(): void
    {
        $this->resetPage();
    }

    public function updatingMaxPrice(): void
    {
        $this->resetPage();
    }

    public function updatingDurationFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $minPrice = is_numeric($this->minPrice) ? (int) $this->minPrice : 0;
        $maxPrice = is_numeric($this->maxPrice) && $this->maxPrice !== '' ? (int) $this->maxPrice : 999999999;
        $minRating = is_numeric($this->minRating) ? (float) $this->minRating : 0;

        $packages = Package::query()
            ->where('is_active', true)
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->whereBetween('price_per_pax', [$minPrice, $maxPrice])
            ->where('avg_rating', '>=', $minRating)
            ->when($this->durationFilter, fn ($q) => $q->where('duration_days', (int) $this->durationFilter))
            ->paginate(9);

        return view('livewire.customer.package-list', compact('packages'));
    }
}
