<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PartnerApplicationController extends Controller
{
    /**
     * Menampilkan halaman status verifikasi pendaftaran mitra travel.
     */
    public function status(Request $request)
    {
        $user = $request->user();

        if ($user->isAgent() && $user->isActive()) {
            return redirect()->route('agent.dashboard');
        }

        $application = $user->partnerApplication;

        if (!$application) {
            return redirect()->route('home');
        }

        return view('partner.status', compact('application'));
    }
}
