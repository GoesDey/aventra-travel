<?php

namespace App\Livewire\Admin;

use App\Models\Destination;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class DestinationManager extends Component
{
    use WithPagination, WithFileUploads;

    public int $iteration = 0;

    public string $search = '';
    public bool $showModal = false;
    public ?Destination $editing = null;

    public string $name = '';
    public string $description = '';
    public string $description_id = '';
    public string $location = '';
    public string $region = 'selatan';
    public $latitude = null;
    public $longitude = null;
    public int $default_stay_minutes = 120;
    public bool $requires_sea_crossing = false;
    public bool $is_active = true;
    public $image;

    public function mount(): void
    {
        $this->authorizeSuperAdmin();
    }

    private function authorizeSuperAdmin(): void
    {
        if (!auth()->user()?->isSuperAdmin()) {
            abort(403, __('Access denied. Only Super Admin can manage destinations.'));
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'name'                  => 'required|string|max:255',
            'description'           => 'required|string',
            'description_id'        => 'nullable|string',
            'location'              => 'required|string|max:150',
            'region'                => 'required|in:utara,selatan,timur,barat',
            'latitude'              => 'required|numeric|between:-8.95,-8.00',
            'longitude'             => 'required|numeric|between:114.40,115.75',
            'default_stay_minutes'  => 'required|integer|min:30|max:720',
            'requires_sea_crossing' => 'boolean',
            'is_active'             => 'boolean',
            'image'                 => 'nullable|image|max:2048',
        ];
    }

    protected function messages(): array
    {
        return [
            'latitude.required'  => __('Latitude is required. Please pick a point on the map or enter coordinates.'),
            'latitude.between'   => __('Latitude must be within Bali area (-8.95 to -8.00).'),
            'longitude.required' => __('Longitude is required. Please pick a point on the map or enter coordinates.'),
            'longitude.between'  => __('Longitude must be within Bali area (114.40 to 115.75).'),
        ];
    }

    public function setCoordinatesFromMap(float $lat, float $lng, ?string $suggestedLocation = null): void
    {
        $this->authorizeSuperAdmin();
        $this->latitude = round($lat, 6);
        $this->longitude = round($lng, 6);
        $this->region = Destination::inferRegionFromCoordinates($this->latitude, $this->longitude);

        if ($suggestedLocation && trim($this->location) === '') {
            $this->location = Str::limit($suggestedLocation, 150, '');
        }

        $this->resetValidation(['latitude', 'longitude', 'region']);
    }

    public function updatedLatitude(): void
    {
        $this->syncRegionFromInputs();
    }

    public function updatedLongitude(): void
    {
        $this->syncRegionFromInputs();
    }

    private function syncRegionFromInputs(): void
    {
        if (is_numeric($this->latitude) && is_numeric($this->longitude)) {
            $lat = (float) $this->latitude;
            $lng = (float) $this->longitude;
            if ($lat >= -8.95 && $lat <= -8.00 && $lng >= 114.40 && $lng <= 115.75) {
                $this->region = Destination::inferRegionFromCoordinates($lat, $lng);
            }
        }
    }

    public function openCreate(): void
    {
        $this->authorizeSuperAdmin();
        $this->reset([
            'name',
            'description',
            'description_id',
            'location',
            'image',
            'editing',
        ]);
        $this->region = 'selatan';
        $this->latitude = -8.650000;
        $this->longitude = 115.216667;
        $this->default_stay_minutes = 120;
        $this->requires_sea_crossing = false;
        $this->is_active = true;
        $this->resetValidation();
        $this->iteration++;
        $this->showModal = true;

        $this->dispatch('destination-map-open', lat: (float) $this->latitude, lng: (float) $this->longitude);
    }

    public function openEdit(Destination $destination): void
    {
        $this->authorizeSuperAdmin();
        $this->reset('image');
        $this->resetValidation();
        $this->iteration++;

        $this->editing = $destination;
        $this->name = $destination->name;
        $this->description = $destination->description;
        $this->description_id = (string) ($destination->description_id ?? '');
        $this->location = $destination->location;
        $this->region = $destination->region ?? 'selatan';
        $this->latitude = $destination->latitude !== null ? round((float) $destination->latitude, 6) : null;
        $this->longitude = $destination->longitude !== null ? round((float) $destination->longitude, 6) : null;
        $this->default_stay_minutes = (int) ($destination->default_stay_minutes ?? 120);
        $this->requires_sea_crossing = (bool) $destination->requires_sea_crossing;
        $this->is_active = (bool) $destination->is_active;
        $this->showModal = true;

        $this->dispatch(
            'destination-map-open',
            lat: $this->latitude !== null ? (float) $this->latitude : -8.650000,
            lng: $this->longitude !== null ? (float) $this->longitude : 115.216667
        );
    }

    public function toggleActive(Destination $destination): void
    {
        $this->authorizeSuperAdmin();
        $destination->update(['is_active' => !$destination->is_active]);
        session()->flash('success', __('Destination status updated.'));
    }

    public function save(): void
    {
        $this->authorizeSuperAdmin();
        $validated = $this->validate();

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;
        $existingId = $this->editing?->id;
        while (Destination::where('slug', $slug)->where('id', '!=', $existingId)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $validated['slug'] = $slug;
        $validated['description_id'] = trim((string) ($validated['description_id'] ?? '')) !== ''
            ? trim($validated['description_id'])
            : null;

        if ($this->image) {
            $validated['image_path'] = $this->image->store('destinations', 'public');
        }
        unset($validated['image']);

        $isEditing = (bool) $this->editing;

        $this->editing
            ? $this->editing->update($validated)
            : Destination::create($validated);

        $this->showModal = false;
        $this->reset(['name', 'description', 'description_id', 'location', 'image', 'editing']);
        session()->flash('success', $isEditing ? __('Destination updated successfully') : __('Destination added successfully'));
    }

    public function delete(Destination $destination): void
    {
        $this->authorizeSuperAdmin();
        $destination->delete();
        session()->flash('success', __('Destination deleted successfully'));
    }

    public function render()
    {
        return view('livewire.admin.destination-manager', [
            'destinations' => Destination::where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('location', 'like', "%{$this->search}%");
            })
                ->latest()
                ->paginate(10),
        ])->layout('components.layouts.admin');
    }
}
