<?php

namespace App\Livewire\Admin;

use App\Models\Package;
use Livewire\Component;
use Livewire\WithPagination;

class PackageManager extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $detailPackageId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function showDetail(int $packageId): void
    {
        $this->detailPackageId = $packageId;
    }

    public function closeDetail(): void
    {
        $this->detailPackageId = null;
    }

    public function toggleActive(Package $package): void
    {
        $package->update(['is_active' => !$package->is_active]);
        session()->flash('success', $package->is_active
            ? __('Package has been activated.')
            : __('Package has been deactivated.'));
    }

    public function openCreate(): void
    {
        abort(403, __('Super Admin cannot create tour packages. Packages are managed by Partner Agents.'));
    }

    public function openEdit(Package $package): void
    {
        abort(403, __('Super Admin cannot edit partner package content. You may only view details or toggle active status.'));
    }

    public function save(): void
    {
        abort(403, __('Super Admin cannot modify partner package content.'));
    }

    public function delete(Package $package): void
    {
        if (auth()->user()?->isSuperAdmin()) {
            abort(403, __('Super Admin cannot delete partner packages. Please deactivate the package instead.'));
        }

        if ($package->bookings()->exists()) {
            session()->flash('error', __('This package cannot be deleted because it has existing booking history. Please deactivate it instead.'));
            return;
        }

        $package->delete();
        session()->flash('success', __('Package deleted successfully.'));
    }

    public function render()
    {
        $detailPackage = $this->detailPackageId
            ? Package::with(['agent.user', 'destinations', 'schedules'])->withCount('bookings')->find($this->detailPackageId)
            : null;

        return view('livewire.admin.package-manager', [
            'packages' => Package::with(['destinations', 'agent'])
                ->withCount(['bookings', 'schedules'])
                ->where('name', 'like', "%{$this->search}%")
                ->latest()
                ->paginate(10),
            'detailPackage' => $detailPackage,
        ])->layout('components.layouts.admin');
    }
}
