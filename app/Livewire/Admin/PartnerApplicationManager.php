<?php

namespace App\Livewire\Admin;

use App\Models\Agent;
use App\Models\PartnerApplication;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PartnerApplicationManager extends Component
{
    use WithPagination;

    public string $statusFilter = '';
    public ?int $detailApplicationId = null;
    public ?int $rejectApplicationId = null;
    public string $rejectionReason = '';

    public function mount(): void
    {
        $this->authorizeSuperAdmin();
    }

    private function authorizeSuperAdmin(): void
    {
        if (!auth()->user()?->isSuperAdmin()) {
            abort(403, __('Access denied. Only Super Admin can review partner applications.'));
        }
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function showDetail(int $applicationId): void
    {
        $this->authorizeSuperAdmin();
        $this->detailApplicationId = $applicationId;
    }

    public function closeDetail(): void
    {
        $this->detailApplicationId = null;
    }

    public function openReject(int $applicationId): void
    {
        $this->authorizeSuperAdmin();
        $this->rejectApplicationId = $applicationId;
        $this->rejectionReason = '';
        $this->resetValidation('rejectionReason');
    }

    public function closeReject(): void
    {
        $this->rejectApplicationId = null;
        $this->rejectionReason = '';
        $this->resetValidation('rejectionReason');
    }

    public function approve(int $applicationId): void
    {
        $this->authorizeSuperAdmin();

        $approvedName = null;

        DB::transaction(function () use ($applicationId, &$approvedName) {
            $application = PartnerApplication::with('user')
                ->where('id', $applicationId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($application->status !== 'pending') {
                return;
            }

            $application->update([
                'status'           => 'approved',
                'rejection_reason' => null,
                'reviewed_by'      => auth()->id(),
                'reviewed_at'      => now(),
            ]);

            $user = $application->user;
            if ($user) {
                $user->update([
                    'role'   => 'agent',
                    'status' => 'active',
                ]);

                Agent::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'agent_name'          => $application->business_name,
                        'description'         => 'Verified TriHitaTour partner travel agent.',
                        'address'             => 'Bali, Indonesia',
                        'phone_number'        => $application->whatsapp_number,
                        'vehicle_count'       => 1,
                        'operating_regions'   => $application->operating_regions ?? [],
                        'nib_number'          => $application->nib_number,
                        'bank_name'           => $application->bank_name,
                        'bank_account_number' => $application->bank_account_number,
                        'bank_account_name'   => $application->bank_account_name,
                    ]
                );
            }

            $approvedName = $application->business_name;
        }, 3);

        $this->detailApplicationId = null;

        if ($approvedName) {
            session()->flash('success', __('Partner application for :business has been approved. The account is now an active Partner Agent.', ['business' => $approvedName]));
        }
    }

    public function reject(): void
    {
        $this->authorizeSuperAdmin();

        $this->validate([
            'rejectionReason' => 'required|string|min:5|max:1000',
        ], [
            'rejectionReason.required' => __('Please provide a reason for rejection.'),
            'rejectionReason.min'      => __('Rejection reason must be at least 5 characters.'),
        ]);

        $rejectedName = null;

        DB::transaction(function () use (&$rejectedName) {
            $application = PartnerApplication::where('id', $this->rejectApplicationId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($application->status !== 'pending') {
                return;
            }

            $application->update([
                'status'           => 'rejected',
                'rejection_reason' => trim($this->rejectionReason),
                'reviewed_by'      => auth()->id(),
                'reviewed_at'      => now(),
            ]);

            $rejectedName = $application->business_name;
        }, 3);

        $this->closeReject();
        $this->detailApplicationId = null;

        if ($rejectedName) {
            session()->flash('success', __('Partner application for :business has been rejected.', ['business' => $rejectedName]));
        }
    }

    public function render()
    {
        $detailApplication = $this->detailApplicationId
            ? PartnerApplication::with(['user', 'reviewer'])->find($this->detailApplicationId)
            : null;

        $rejectApplication = $this->rejectApplicationId
            ? PartnerApplication::with('user')->find($this->rejectApplicationId)
            : null;

        return view('livewire.admin.partner-application-manager', [
            'applications'      => PartnerApplication::with(['user', 'reviewer'])
                ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
                ->latest()
                ->paginate(12),
            'pendingCount'      => PartnerApplication::where('status', 'pending')->count(),
            'detailApplication' => $detailApplication,
            'rejectApplication' => $rejectApplication,
        ])->layout('components.layouts.admin');
    }
}

