<?php

namespace App\Livewire\Admin;

use App\Models\Package;
use App\Models\Destination;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class PackageManager extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public bool $showModal = false;
    public ?Package $editing = null;

    public string $name = '';
    public string $description = '';
    public $price_per_pax = 0;
    public $duration_days = 1;
    public $max_guests = 10;
    public bool $is_active = true;
    public $image;
    public array $selectedDestinations = [];

    protected function rules(): array
    {
        $editingId = $this->editing?->id;
        return [
            'name'          => 'required|string|max:255',
            'description'   => 'required|string',
            'price_per_pax' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_guests'    => 'required|integer|min:1',
            'is_active'     => 'boolean',
            'image'         => 'nullable|image|max:2048',
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['name', 'description', 'price_per_pax', 'duration_days', 'max_guests', 'is_active', 'image', 'selectedDestinations', 'editing']);
        $this->duration_days = 1;
        $this->max_guests = 10;
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEdit(Package $package): void
    {
        $this->editing = $package;
        $this->name = $package->name;
        $this->description = $package->description;
        $this->price_per_pax = $package->price_per_pax;
        $this->duration_days = $package->duration_days;
        $this->max_guests = $package->max_guests;
        $this->is_active = (bool) $package->is_active;
        $this->selectedDestinations = $package->destinations()->pluck('destinations.id')->map(fn ($id) => (string) $id)->toArray();
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        // Generate a unique slug (append suffix if duplicate)
        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;
        $existingId = $this->editing?->id;
        while (Package::where('slug', $slug)->where('id', '!=', $existingId)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $validated['slug'] = $slug;

        if ($this->image) {
            $validated['cover_image'] = $this->image->store('packages', 'public');
        }

        $isEditing = (bool) $this->editing;

        $package = $this->editing;
        if ($package) {
            $package->update($validated);
        } else {
            // Explicitly set is_active so new packages are always visible
            $validated['is_active'] = $this->is_active;
            $package = Package::create($validated);
        }

        // Cast IDs to integers for proper pivot sync
        $syncData = collect($this->selectedDestinations)
            ->map(fn ($id) => (int) $id)
            ->values()
            ->mapWithKeys(fn ($id, $order) => [$id => ['visit_order' => $order + 1]]);

        $package->destinations()->sync($syncData);

        $this->showModal = false;
        $this->reset(['name', 'description', 'price_per_pax', 'duration_days', 'max_guests', 'is_active', 'image', 'selectedDestinations', 'editing']);
        $this->duration_days = 1;
        $this->max_guests = 10;
        $this->is_active = true;
        session()->flash('success', $isEditing ? 'Paket berhasil diperbarui' : 'Paket berhasil ditambahkan');
    }

    public function delete(Package $package): void
    {
        $package->delete();
        session()->flash('success', 'Paket berhasil dihapus');
    }

    public function render()
    {
        return view('livewire.admin.package-manager', [
            'packages' => Package::with('destinations')
                ->where('name', 'like', "%{$this->search}%")
                ->latest()
                ->paginate(10),
            'availableDestinations' => Destination::all(),
        ])->layout('components.layouts.admin');
    }
}
