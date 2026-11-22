<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class AgentBookingController extends Controller
{
    /**
     * Memperbarui status booking oleh Agen.
     * Backend Guard: Hanya diizinkan dibayar -> berjalan -> selesai.
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $agent = auth()->user()?->agent;
        if (!$agent || $booking->package->agent_id !== $agent->id) {
            abort(403, 'Access denied. This booking does not belong to your tour packages.');
        }

        $validated = $request->validate([
            'status' => 'required|string|in:berjalan,selesai,dibatalkan',
        ], [
            'status.required' => __('Target status is required.'),
            'status.in'       => __('Agents are only permitted to change status to ongoing, completed, or cancelled.'),
        ]);

        try {
            $booking->transitionStatusByAgent($validated['status']);
        } catch (\DomainException $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }

        $statusLabel = Booking::statusLabel($validated['status']);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('Booking status successfully updated to :status.', ['status' => $statusLabel]),
                'booking' => $booking->fresh(),
            ]);
        }

        return back()->with('success', __('Booking status successfully updated to :status.', ['status' => $statusLabel]));
    }
}
