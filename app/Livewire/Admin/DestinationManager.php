<?php

namespace App\Livewire\Admin;

use App\Models\Destination;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class DestinationManager extends Component
{
    use WithPagination, WithFileUploads;

    public int $iteration = 0;

    public string $search = '';
    public bool $showModal = false;
    public ?Destination $editing = null;

    public string $name = '';
    public string $description = '';
    public string $location = '';
    public $image;

    protected function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'location'    => 'required|string|max:150',
            'image'       => 'nullable|image|max:2048',
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['name', 'description', 'location', 'image', 'editing']);
        $this->iteration++;
        $this->showModal = true;
    }

    public function openEdit(Destination $destination): void
    {
        $this->reset('image');
        $this->iteration++;
        $this->editing = $destination;
        $this->name = $destination->name;
        $this->description = $destination->description;
        $this->location = $destination->location;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();
        $validated['slug'] = Str::slug($validated['name']);
         
        if ($this->image) {
            $validated['image_path'] = $this->image->store('destinations', 'public');
        }


        $isEditing = (bool) $this->editing;

        $this->editing
            ? $this->editing->update($validated)
            : Destination::create($validated);

        $this->showModal = false;
        $this->reset(['name', 'description', 'location', 'image', 'editing']);
        session()->flash('success', $isEditing ? 'Destinasi berhasil diperbarui' : 'Destinasi berhasil ditambahkan');
    }

    public function delete(Destination $destination): void
    {
        $destination->delete();
        session()->flash('success', 'Destinasi berhasil dihapus');
    }

    public function render()
    {
        return view('livewire.admin.destination-manager', [
            'destinations' => Destination::where('name', 'like', "%{$this->search}%")
                ->orWhere('location', 'like', "%{$this->search}%")
                ->latest()
                ->paginate(10),
        ])->layout('components.layouts.admin');
    }
}
