<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RtRwController extends Controller
{
    public function index(): View
    {
        $rws = Rw::orderBy('nomor_rw', 'asc')->get();
        $rts = Rt::with('rw')->orderBy('nomor_rt', 'asc')->get();
        $totalRT = $rts->count();
        $totalRW = $rws->count();
        return view('admin.data_perangkat_index', compact('rws', 'rts', 'totalRT', 'totalRW'));
    }

    public function create(): View
    {
        $rws = Rw::orderBy('nomor_rw', 'asc')->get();
        return view('admin.input_perangkat', compact('rws'));
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->input('type') === 'rw') {
            $validated = $request->validate([
                'nomor_rw' => 'required|integer|unique:rw,nomor_rw',
                'ketua_rw' => 'required|string|max:255'
            ], [
                'nomor_rw.unique' => 'Nomor RW ini sudah terdaftar. Gagal menambahkan data.'
            ]);
            
            Rw::create($validated);
            $message = "Data RW {$validated['nomor_rw']} ({$validated['ketua_rw']}) berhasil ditambahkan!";

        } elseif ($request->input('type') === 'rt') {
            $validated = $request->validate([
                'rw_id' => 'required|exists:rw,id',
                'nomor_rt' => [
                    'required',
                    'integer',
                    Rule::unique('rt', 'nomor_rt')->where(fn ($query) => $query->where('rw_id', $request->rw_id)),
                ],
                'ketua_rt' => 'required|string|max:255'
            ], [
                'nomor_rt.unique' => 'Nomor RT ini sudah terdaftar di dalam RW yang dipilih. Gagal menambahkan data.'
            ]);
            
            $rt = Rt::create($validated);
            $nomorRw = $rt->rw->nomor_rw; 
            $message = "Data RT {$validated['nomor_rt']} di RW {$nomorRw} ({$validated['ketua_rt']}) berhasil ditambahkan!";

        } else {
            return back()->with('error', 'Tipe data tidak valid.');
        }

        return redirect()->route('admin.rt-rw.index')->with('success', $message);
    }

    public function edit(string $type, int $id): View
    {
        if ($type === 'rw') $item = Rw::findOrFail($id);
        elseif ($type === 'rt') $item = Rt::findOrFail($id);
        else abort(404);

        $rws = Rw::orderBy('nomor_rw', 'asc')->get();
        return view('admin.edit_perangkat', compact('item', 'type', 'rws'));
    }

    /**
     * Memperbarui data RT atau RW.
     */
    public function update(Request $request, string $type, int $id): RedirectResponse
    {
        if ($type === 'rw') {
            $item = Rw::findOrFail($id);
            $validated = $request->validate([
                // Pastikan nomor_rw unik, kecuali untuk item ini ($id)
                'nomor_rw' => 'required|integer|unique:rw,nomor_rw,' . $id,
                'ketua_rw' => 'required|string|max:255'
            ], [
                'nomor_rw.unique' => 'Nomor RW ini sudah terdaftar. Gagal memperbarui data.'
            ]);
            
            $item->update($validated);
            // UPDATE PESAN SUKSES
            $message = "Data RW {$validated['nomor_rw']} ({$validated['ketua_rw']}) berhasil diperbarui!";

        } elseif ($type === 'rt') {
            $item = Rt::findOrFail($id);
            $validated = $request->validate([
                'rw_id' => 'required|exists:rw,id',
                // Pastikan nomor_rt unik di dalam rw_id yang sama, kecuali untuk item ini ($id)
                'nomor_rt' => [
                    'required',
                    'integer',
                    Rule::unique('rt', 'nomor_rt')->ignore($id)->where(fn ($query) => $query->where('rw_id', $request->rw_id)),
                ],
                'ketua_rt' => 'required|string|max:255'
            ], [
                'nomor_rt.unique' => 'Nomor RT sudah ada di dalam RW yang dipilih. Gagal memperbarui data.'
            ]);
            
            $item->update($validated);
            // Ambil nomor RW untuk pesan sukses
            $nomorRw = $item->rw->nomor_rw;
            // UPDATE PESAN SUKSES
            $message = "Data RT {$validated['nomor_rt']} di RW {$nomorRw} ({$validated['ketua_rt']}) berhasil diperbarui!";

        } else {
            return back()->with('error', 'Tipe data tidak valid.');
        }

        return redirect()->route('admin.rt-rw.index')->with('success', $message);
    }

    /**
     * Menghapus data RT atau RW.
     */
    public function destroy(string $type, int $id): RedirectResponse
    {
        if ($type === 'rw') {
            $item = Rw::findOrFail($id);
            // Pengecekan dependensi RT
            if ($item->rt()->exists()) {
                return back()->with('error', 'Gagal menghapus! RW ini masih memiliki data RT terkait. Mohon hapus data RT tersebut terlebih dahulu.');
            }
            // Pengecekan dependensi Penduduk (Opsional, tergantung implementasi)
            if ($item->penduduks()->exists()) {
                return back()->with('error', 'Gagal menghapus! RW ini masih memiliki data penduduk terkait.');
            }
            $item->delete();
            $message = 'Data RW berhasil dihapus!';

        } elseif ($type === 'rt') {
            $item = Rt::findOrFail($id);
            // Tambahkan pengecekan jika RT masih memiliki data penduduk
            // ASUMSI: Anda memiliki relasi penduduk() di model Rt
            if ($item->penduduk()->exists()) {
                return back()->with('error', 'Gagal menghapus! RT ini masih memiliki data penduduk terkait. Mohon hapus data penduduk tersebut terlebih dahulu.');
            }
            $item->delete();
            $message = 'Data RT berhasil dihapus!';

        } else {
            return back()->with('error', 'Tipe data tidak valid.');
        }

        return redirect()->route('admin.rt-rw.index')->with('success', $message);
    }
}