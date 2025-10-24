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
    public function index(): View
    {
        $komoditas = Komoditas::orderBy('nama_komoditas')->get();
        $totalKomoditas = $komoditas->count();
        $totalKategori = $komoditas->pluck('kategori')->unique()->count();
        return view('admin.data_komoditas_index', compact('komoditas', 'totalKomoditas', 'totalKategori'));
    }

    /**
     * Menampilkan form untuk menambah komoditas baru.
     */
    public function create(): View
    {
        return view('admin.input_komoditas');
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
    public function edit(Komoditas $komoditas): View
    {
        return view('admin.edit_komoditas', compact('komoditas'));
    }

    /**
     * Memperbarui data komoditas.
     */
    public function update(Request $request, Komoditas $komoditas): RedirectResponse
    {
        $validatedData = $request->validate($this->validationRules());

        if (isset($validatedData['harga'])) {
            $validatedData['harga'] = preg_replace('/[^0-9]/', '', $validatedData['harga']);
        }

        $komoditas->update($validatedData);
        return redirect()->route('admin.komoditas.index')->with('success', 'Data Komoditas berhasil diperbarui!');
    }

    /**
     * Menghapus data komoditas.
     */
    public function destroy(Komoditas $komoditas): RedirectResponse
    {
        $komoditas->delete();
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
