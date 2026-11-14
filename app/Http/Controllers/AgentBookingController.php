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
            'status' => 'required|string|in:berjalan,selesai',
        ], [
            'status.required' => 'Target status is required.',
            'status.in'       => 'Agents are only permitted to change status to ongoing or completed.',
        ]);

        try {
            $booking->transitionStatusByAgent($validated['status']);
        } catch (\DomainException $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => "Booking status successfully updated to {$validated['status']}.",
                'booking' => $booking->fresh(),
            ]);
        }

        return back()->with('success', "Booking status successfully updated to {$validated['status']}.");
    }
}
