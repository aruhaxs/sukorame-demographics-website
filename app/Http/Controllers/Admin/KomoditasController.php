<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Komoditas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KomoditasController extends Controller
{
    /**
     * Menampilkan daftar komoditas.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $query = Komoditas::query();

        if ($search) {
            $query->where('nama_komoditas', 'like', '%' . $search . '%')
                  ->orWhere('kategori', 'like', '%' . $search . '%')
                  ->orWhere('produsen', 'like', '%' . $search . '%');
        }

        $komoditas = $query->orderBy('nama_komoditas')
                           ->paginate(12)
                           ->appends($request->query());

        $totalKomoditas = Komoditas::count();
        $totalKategori = Komoditas::distinct('kategori')->count('kategori');

        return view('admin.data_komoditas_index', compact(
            'komoditas',
            'totalKomoditas',
            'totalKategori'
        ));
    }

    /**
     * Menampilkan form untuk menambah komoditas baru.
     */
    public function create(): View
    {
        $totalKomoditas = Komoditas::count();
        $totalKategori = Komoditas::distinct('kategori')->count('kategori');

        return view('admin.input_komoditas', compact('totalKomoditas', 'totalKategori'));
    }

    /**
     * Menyimpan data komoditas baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate($this->validationRules());

        if (isset($validatedData['harga'])) {
            $validatedData['harga'] = preg_replace('/[^0-9]/', '', $validatedData['harga']);
        }

        Komoditas::create($validatedData);
        return redirect()->route('admin.komoditas.index')->with('success', 'Data Komoditas berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit data komoditas.
     */
    // ===========================================
    // PERBAIKAN 1: Ganti $komoditas -> $komodita
    // ===========================================
    public function edit(Komoditas $komodita): View
    {
        $totalKomoditas = Komoditas::count();
        $totalKategori = Komoditas::distinct('kategori')->count('kategori');

        // Kirim variabel 'komodita' (tanpa 's') ke view
        return view('admin.edit_komoditas', compact('komodita', 'totalKomoditas', 'totalKategori'));
    }

    /**
     * Memperbarui data komoditas.
     */
    // ===========================================
    // PERBAIKAN 2: Ganti $komoditas -> $komodita
    // ===========================================
    public function update(Request $request, Komoditas $komodita): RedirectResponse
    {
        $validatedData = $request->validate($this->validationRules());

        if (isset($validatedData['harga'])) {
            $validatedData['harga'] = preg_replace('/[^0-9]/', '', $validatedData['harga']);
        }

        // Gunakan $komodita (tanpa 's') untuk update
        $komodita->update($validatedData);
        return redirect()->route('admin.komoditas.index')->with('success', 'Data Komoditas berhasil diperbarui!');
    }

    /**
     * Menghapus data komoditas.
     */
    // ===========================================
    // PERBAIKAN 3: Ganti $komoditas -> $komodita
    // ===========================================
    public function destroy(Komoditas $komodita): RedirectResponse
    {
        $komodita->delete();
        return redirect()->route('admin.komoditas.index')->with('success', 'Data komoditas berhasil dihapus!');
    }

    /**
     * Aturan validasi untuk komoditas.
     */
    private function validationRules(): array
    {
        return [
            'nama_komoditas' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'produksi' => 'nullable|string|max:255',
            'periode' => 'nullable|string|max:255',
            'produsen' => 'nullable|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'harga' => 'nullable|string|max:255',
        ];
    }
}