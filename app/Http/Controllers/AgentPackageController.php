<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Destination;
use App\Models\PackageSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AgentPackageController extends Controller
{
    /**
     * Dapatkan ID Agen milik user yang sedang login.
     */
    protected function getAgentId(): int
    {
        $agent = auth()->user()?->agent;

        if (!$agent) {
            abort(403, 'Akses ditolak. Profil agen belum terdaftar.');
        }

        return $agent->id;
    }

    /**
     * Menampilkan daftar paket wisata milik agen.
     */
    public function index()
    {
        $agentId = $this->getAgentId();

        $packages = Package::where('agent_id', $agentId)
            ->with(['schedules', 'destinations'])
            ->withCount('schedules')
            ->latest()
            ->paginate(10);

        return view('agent.packages.index', compact('packages'));
    }

    /**
     * Menampilkan form pembuatan paket wisata baru.
     */
    public function create()
    {
        $agentId = $this->getAgentId();

        // Ambil destinasi milik agen atau destinasi umum
        $destinations = Destination::where(function ($query) use ($agentId) {
            $query->where('agent_id', $agentId)->orWhereNull('agent_id');
        })->get();

        return view('agent.packages.create', compact('destinations'));
    }

    /**
     * Menyimpan paket wisata baru beserta jadwal (package_schedules).
     */
    public function store(Request $request)
    {
        $agentId = $this->getAgentId();

        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'description'            => 'required|string',
            'price_per_pax'          => 'required|numeric|min:0',
            'duration_days'          => 'required|integer|min:1',
            'max_guests'             => 'required|integer|min:1',
            'category'               => 'required|in:reguler,custom',
            'region'                 => 'required|in:timur,barat,lainnya',
            'cover_image'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active'              => 'nullable|boolean',
            'destinations'           => 'nullable|array',
            'destinations.*'         => 'exists:destinations,id',
            'schedules'              => 'required|array|min:1',
            'schedules.*.start_date' => 'required|date',
            'schedules.*.end_date'   => 'required|date|after_or_equal:schedules.*.start_date',
            'schedules.*.quota'      => 'required|integer|min:1',
        ], [
            'name.required'                   => 'Nama paket wajib diisi.',
            'description.required'            => 'Deskripsi paket wajib diisi.',
            'price_per_pax.required'          => 'Harga per pax wajib diisi.',
            'category.required'               => 'Kategori paket wajib dipilih.',
            'region.required'                 => 'Region destinasi wajib dipilih.',
            'schedules.required'              => 'Minimal satu jadwal perjalanan wajib ditambahkan.',
            'schedules.*.start_date.required' => 'Tanggal mulai jadwal wajib diisi.',
            'schedules.*.end_date.required'   => 'Tanggal selesai jadwal wajib diisi.',
            'schedules.*.end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'schedules.*.quota.required'      => 'Kuota peserta jadwal wajib diisi.',
        ]);

        // Generate unique slug
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        while (Package::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        // Upload cover image jika ada
        $coverImagePath = null;
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $request->file('cover_image')->store('packages', 'public');
        }

        DB::transaction(function () use ($validated, $slug, $coverImagePath, $agentId, $request) {
            $package = Package::create([
                'agent_id'      => $agentId,
                'name'          => $validated['name'],
                'slug'          => $slug,
                'description'   => $validated['description'],
                'category'      => $validated['category'],
                'region'        => $validated['region'],
                'price_per_pax' => $validated['price_per_pax'],
                'duration_days' => $validated['duration_days'],
                'max_guests'    => $validated['max_guests'],
                'cover_image'   => $coverImagePath,
                'is_active'     => $request->boolean('is_active', true),
            ]);

            // Simpan relasi destinasi jika dipilih
            if (!empty($validated['destinations'])) {
                $attachData = [];
                foreach ($validated['destinations'] as $order => $destId) {
                    $attachData[$destId] = ['visit_order' => $order + 1];
                }
                $package->destinations()->sync($attachData);
            }

            // Simpan jadwal (package_schedules)
            foreach ($validated['schedules'] as $scheduleData) {
                $package->schedules()->create([
                    'start_date' => $scheduleData['start_date'],
                    'end_date'   => $scheduleData['end_date'],
                    'quota'      => $scheduleData['quota'],
                ]);
            }
        });

        return redirect()->route('agent.packages.index')->with('success', 'Paket wisata berhasil dibuat beserta jadwal perjalanannya.');
    }

    /**
     * Menampilkan form edit paket wisata milik agen.
     */
    public function edit(string $id)
    {
        $agentId = $this->getAgentId();

        $package = Package::where('agent_id', $agentId)
            ->with(['schedules', 'destinations'])
            ->findOrFail($id);

        $destinations = Destination::where(function ($query) use ($agentId) {
            $query->where('agent_id', $agentId)->orWhereNull('agent_id');
        })->get();

        return view('agent.packages.edit', compact('package', 'destinations'));
    }

    /**
     * Memperbarui paket wisata milik agen dan memperbarui package_schedules.
     */
    public function update(Request $request, string $id)
    {
        $agentId = $this->getAgentId();

        $package = Package::where('agent_id', $agentId)->findOrFail($id);

        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'description'            => 'required|string',
            'price_per_pax'          => 'required|numeric|min:0',
            'duration_days'          => 'required|integer|min:1',
            'max_guests'             => 'required|integer|min:1',
            'category'               => 'required|in:reguler,custom',
            'region'                 => 'required|in:timur,barat,lainnya',
            'cover_image'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active'              => 'nullable|boolean',
            'destinations'           => 'nullable|array',
            'destinations.*'         => 'exists:destinations,id',
            'schedules'              => 'required|array|min:1',
            'schedules.*.id'         => 'nullable|integer',
            'schedules.*.start_date' => 'required|date',
            'schedules.*.end_date'   => 'required|date|after_or_equal:schedules.*.start_date',
            'schedules.*.quota'      => 'required|integer|min:1',
        ], [
            'name.required'                   => 'Nama paket wajib diisi.',
            'description.required'            => 'Deskripsi paket wajib diisi.',
            'price_per_pax.required'          => 'Harga per pax wajib diisi.',
            'category.required'               => 'Kategori paket wajib dipilih.',
            'region.required'                 => 'Region destinasi wajib dipilih.',
            'schedules.required'              => 'Minimal satu jadwal perjalanan wajib ditambahkan.',
            'schedules.*.start_date.required' => 'Tanggal mulai jadwal wajib diisi.',
            'schedules.*.end_date.required'   => 'Tanggal selesai jadwal wajib diisi.',
            'schedules.*.end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'schedules.*.quota.required'      => 'Kuota peserta jadwal wajib diisi.',
        ]);

        DB::transaction(function () use ($validated, $package, $request) {
            $updateData = [
                'name'          => $validated['name'],
                'description'   => $validated['description'],
                'category'      => $validated['category'],
                'region'        => $validated['region'],
                'price_per_pax' => $validated['price_per_pax'],
                'duration_days' => $validated['duration_days'],
                'max_guests'    => $validated['max_guests'],
                'is_active'     => $request->boolean('is_active', true),
            ];

            // Update slug jika nama berubah
            if ($package->name !== $validated['name']) {
                $slug = Str::slug($validated['name']);
                $originalSlug = $slug;
                $counter = 1;
                while (Package::where('slug', $slug)->where('id', '!=', $package->id)->exists()) {
                    $slug = "{$originalSlug}-{$counter}";
                    $counter++;
                }
                $updateData['slug'] = $slug;
            }

            // Ganti foto cover jika ada upload baru
            if ($request->hasFile('cover_image')) {
                if ($package->cover_image && Storage::disk('public')->exists($package->cover_image)) {
                    Storage::disk('public')->delete($package->cover_image);
                }
                $updateData['cover_image'] = $request->file('cover_image')->store('packages', 'public');
            }

            $package->update($updateData);

            // Sync destinasi
            if (isset($validated['destinations'])) {
                $attachData = [];
                foreach ($validated['destinations'] as $order => $destId) {
                    $attachData[$destId] = ['visit_order' => $order + 1];
                }
                $package->destinations()->sync($attachData);
            } else {
                $package->destinations()->detach();
            }

            // Sinkronisasi jadwal (package_schedules):
            // 1. Hapus jadwal yang tidak lagi ada di input form
            $submittedScheduleIds = collect($validated['schedules'])
                ->pluck('id')
                ->filter()
                ->map(fn($val) => (int) $val)
                ->toArray();

            $package->schedules()->whereNotIn('id', $submittedScheduleIds)->delete();

            // 2. Update jadwal yang sudah ada atau buat baru
            foreach ($validated['schedules'] as $scheduleData) {
                if (!empty($scheduleData['id']) && in_array((int)$scheduleData['id'], $submittedScheduleIds, true)) {
                    $package->schedules()->where('id', $scheduleData['id'])->update([
                        'start_date' => $scheduleData['start_date'],
                        'end_date'   => $scheduleData['end_date'],
                        'quota'      => $scheduleData['quota'],
                    ]);
                } else {
                    $package->schedules()->create([
                        'start_date' => $scheduleData['start_date'],
                        'end_date'   => $scheduleData['end_date'],
                        'quota'      => $scheduleData['quota'],
                    ]);
                }
            }
        });

        return redirect()->route('agent.packages.index')->with('success', 'Paket wisata dan jadwal berhasil diperbarui.');
    }

    /**
     * Menghapus paket wisata milik agen.
     */
    public function destroy(string $id)
    {
        $agentId = $this->getAgentId();

        $package = Package::where('agent_id', $agentId)->findOrFail($id);

        if ($package->cover_image && Storage::disk('public')->exists($package->cover_image)) {
            Storage::disk('public')->delete($package->cover_image);
        }

        $package->delete();

        return redirect()->route('agent.packages.index')->with('success', 'Paket wisata berhasil dihapus.');
    }
}
