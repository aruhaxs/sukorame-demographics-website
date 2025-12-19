<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bangunan;
use App\Models\Rw;
use App\Models\Rt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class BangunanController extends Controller
{
    public function index(): View
    {
        $bangunansForTable = Bangunan::with('rw', 'rt')->latest()->paginate(10);
        $categoryCounts = Bangunan::select('kategori', DB::raw('COUNT(*) as total'))
            ->groupBy('kategori')
            ->pluck('total', 'kategori');

        return view('admin.data_bangunan_index', [
            'bangunans' => $bangunansForTable,
            'categoryCounts' => $categoryCounts,
            'totalBangunan' => $bangunansForTable->total()
        ]);
    }

    public function create(): View
    {
        $rws = Rw::orderBy('nomor_rw', 'asc')->get();
        return view('admin.input_bangunan', compact('rws'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Validasi: Tambahkan 'alamat'
        $validatedData = $request->validate([
            'nama_bangunan' => 'required|string|max:255',
            'kategori'      => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'alamat'        => 'required|string', // <--- Wajib diisi
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',
            'rw_id'         => 'required|exists:rw,id',
            'rt_id'         => 'required|exists:rt,id',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('bangunan_fotos', 'public');
            $validatedData['foto'] = $path;
        }

        $bangunan = Bangunan::create($validatedData);

        $this->syncToFirebase($bangunan);

        return redirect()->route('admin.bangunan.index')->with('success', 'Data Bangunan berhasil ditambahkan!');
    }

    public function edit(Bangunan $bangunan): View
    {
        $rws = Rw::orderBy('nomor_rw', 'asc')->get();
        $rts = Rt::where('rw_id', $bangunan->rw_id)->orderBy('nomor_rt', 'asc')->get();
        return view('admin.edit_bangunan', compact('bangunan', 'rws', 'rts'));
    }

    public function update(Request $request, Bangunan $bangunan): RedirectResponse
    {
        // Validasi: Tambahkan 'alamat'
        $validatedData = $request->validate([
            'nama_bangunan' => 'required|string|max:255',
            'kategori'      => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'alamat'        => 'required|string', // <--- Wajib diisi
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',
            'rw_id'         => 'required|exists:rw,id',
            'rt_id'         => 'required|exists:rt,id',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($bangunan->foto) {
                Storage::disk('public')->delete($bangunan->foto);
            }
            $path = $request->file('foto')->store('bangunan_fotos', 'public');
            $validatedData['foto'] = $path;
        }

        $bangunan->update($validatedData);

        $this->syncToFirebase($bangunan);

        return redirect()->route('admin.bangunan.index')->with('success', 'Data Bangunan berhasil diperbarui!');
    }

    public function destroy(Bangunan $bangunan): RedirectResponse
    {
        $this->deleteFromFirebase($bangunan->id);

        if ($bangunan->foto) {
            Storage::disk('public')->delete($bangunan->foto);
        }

        $bangunan->delete();
        return redirect()->route('admin.bangunan.index')->with('success', 'Data Bangunan berhasil dihapus!');
    }

    // --- Helper Firebase (Sama seperti sebelumnya) ---
    private function getFirebaseUrl(string $path = ''): ?string
    {
        $base = env('FIREBASE_DATABASE_URL');
        if (empty($base)) return null;
        
        $base = rtrim($base, '/');
        $secret = env('FIREBASE_DB_SECRET');
        $path = ltrim($path, '/');
        $url = "{$base}/{$path}.json";
        
        if (!empty($secret)) {
            $secret = trim($secret, "\"'");
            $url .= '?auth=' . $secret;
        }
        return $url;
    }

    private function syncToFirebase(Bangunan $bangunan): void
    {
        $url = $this->getFirebaseUrl("bangunans/{$bangunan->id}");
        if (!$url) return;

        $data = $bangunan->toArray();
        $disk = Storage::disk('public');
        $data['foto_url'] = $bangunan->foto ? Storage::url($bangunan->foto) : null;

        try {
            Http::timeout(10)->put($url, $data);
        } catch (\Throwable $e) { }
    }

    private function deleteFromFirebase(int $id): void
    {
        $url = $this->getFirebaseUrl("bangunans/{$id}");
        if (!$url) return;
        try {
            Http::timeout(10)->delete($url);
        } catch (\Throwable $e) { }
    }
}