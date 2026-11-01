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
            abort(403, 'Akses ditolak. Booking ini bukan milik paket wisata Anda.');
        }

        $validated = $request->validate([
            'status' => 'required|string|in:berjalan,selesai',
        ], [
            'status.required' => 'Status target wajib dipilih.',
            'status.in'       => 'Agen hanya diizinkan mengubah status menjadi berjalan atau selesai.',
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
                'message' => "Status booking berhasil diperbarui menjadi {$validated['status']}.",
                'booking' => $booking->fresh(),
            ]);
        }

        return back()->with('success', "Status booking berhasil diperbarui menjadi {$validated['status']}.");
    }
}
