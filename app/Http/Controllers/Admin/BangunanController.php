<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bangunan;
use App\Models\Rw;
use App\Models\Rt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- Pastikan ini ada
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BangunanController extends Controller
{
    /**
     * Menampilkan daftar bangunan (TABEL ADMIN).
     * Logika peta sekarang ditangani oleh Api\BangunanMapController.
     */
    public function index(): View
    {
        // 1. Data untuk tabel dengan paginasi
        $bangunansForTable = Bangunan::with('rw', 'rt')->latest()->paginate(10);

        // 2. Data untuk ringkasan
        $categoryCounts = Bangunan::select('kategori', DB::raw('COUNT(*) as total'))
            ->groupBy('kategori')
            ->pluck('total', 'kategori');

        // 3. Kirim data ke view
        return view('admin.data_bangunan_index', [
            'bangunans' => $bangunansForTable, // Ini akan digunakan oleh @forelse dan pagination
            'categoryCounts' => $categoryCounts,
            'totalBangunan' => $bangunansForTable->total()
        ]);
    }

    /**
     * Menampilkan form untuk menambah data bangunan.
     */
    public function create(): View
    {
        $rws = Rw::orderBy('nomor_rw', 'asc')->get();
        return view('admin.input_bangunan', compact('rws'));
    }

    /**
     * Menyimpan data bangunan baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'nama_bangunan' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'rw_id' => 'required|exists:rw,id',
            'rt_id' => 'required|exists:rt,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('bangunan_fotos', 'public');
            $validatedData['foto'] = $path;
        }

        Bangunan::create($validatedData);
        return redirect()->route('admin.bangunan.index')->with('success', 'Data Bangunan berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit data bangunan.
     */
    public function edit(Bangunan $bangunan): View
    {
        $rws = Rw::orderBy('nomor_rw', 'asc')->get();
        $rts = Rt::where('rw_id', $bangunan->rw_id)->orderBy('nomor_rt', 'asc')->get();
        return view('admin.edit_bangunan', compact('bangunan', 'rws', 'rts'));
    }

    /**
     * Memperbarui data bangunan.
     */
    public function update(Request $request, Bangunan $bangunan): RedirectResponse
    {
        $validatedData = $request->validate([
            'nama_bangunan' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'rw_id' => 'required|exists:rw,id',
            'rt_id' => 'required|exists:rt,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($bangunan->foto) {
                Storage::disk('public')->delete($bangunan->foto);
            }
            // Simpan foto baru
            $path = $request->file('foto')->store('bangunan_fotos', 'public');
            $validatedData['foto'] = $path;
        }

        $bangunan->update($validatedData);
        return redirect()->route('admin.bangunan.index')->with('success', 'Data Bangunan berhasil diperbarui!');
    }

    /**
     * Menghapus data bangunan.
     */
    public function destroy(Bangunan $bangunan): RedirectResponse
    {
        if ($bangunan->foto) {
            Storage::disk('public')->delete($bangunan->foto);
        }

        $bangunan->delete();
        return redirect()->route('admin.bangunan.index')->with('success', 'Data Bangunan berhasil dihapus!');
    }
}
