<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RtRwController extends Controller
{
    /**
     * Menampilkan daftar RT dan RW.
     */
    public function index(): View
    {
        $rws = Rw::orderBy('nomor_rw', 'asc')->get();
        $rts = Rt::with('rw')->orderBy('nomor_rt', 'asc')->get();
        $totalRT = $rts->count();
        $totalRW = $rws->count();
        return view('admin.data_perangkat_index', compact('rws', 'rts', 'totalRT', 'totalRW'));
    }

    /**
     * Menampilkan form untuk menambah RT atau RW.
     */
    public function create(): View
    {
        $rws = Rw::orderBy('nomor_rw', 'asc')->get();
        return view('admin.input_perangkat', compact('rws'));
    }

    /**
     * Menyimpan data RT atau RW baru.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->input('type') === 'rw') {
            $validated = $request->validate([
                'nomor_rw' => 'required|integer|unique:rw,nomor_rw',
                'ketua_rw' => 'required|string|max:255'
            ]);
            Rw::create($validated);
            $message = 'Data RW berhasil ditambahkan!';

        } elseif ($request->input('type') === 'rt') {
            $validated = $request->validate([
                'rw_id' => 'required|exists:rw,id',
                'nomor_rt' => 'required|integer|unique:rt,nomor_rt,NULL,id,rw_id,' . $request->rw_id,
                'ketua_rt' => 'required|string|max:255'
            ], [
                'nomor_rt.unique' => 'Nomor RT sudah ada di dalam RW yang dipilih.'
            ]);
            Rt::create($validated);
            $message = 'Data RT berhasil ditambahkan!';

        } else {
            return back()->with('error', 'Tipe data tidak valid.');
        }

        return redirect()->route('admin.rt-rw.index')->with('success', $message);
    }

    /**
     * Menampilkan form untuk mengedit RT atau RW.
     */
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
                'nomor_rw' => 'required|integer|unique:rw,nomor_rw,' . $id,
                'ketua_rw' => 'required|string|max:255'
            ]);
            $item->update($validated);
            $message = 'Data RW berhasil diperbarui!';

        } elseif ($type === 'rt') {
            $item = Rt::findOrFail($id);
            $validated = $request->validate([
                'rw_id' => 'required|exists:rw,id',
                'nomor_rt' => 'required|integer|unique:rt,nomor_rt,' . $id . ',id,rw_id,' . $request->rw_id,
                'ketua_rt' => 'required|string|max:255'
            ], [
                'nomor_rt.unique' => 'Nomor RT sudah ada di dalam RW yang dipilih.'
            ]);
            $item->update($validated);
            $message = 'Data RT berhasil diperbarui!';

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
            if ($item->rt()->exists()) {
                return back()->with('error', 'Gagal menghapus! RW ini masih memiliki data RT terkait.');
            }
            $item->delete();
            $message = 'Data RW berhasil dihapus!';

        } elseif ($type === 'rt') {
            $item = Rt::findOrFail($id);
            // Tambahkan pengecekan jika RT masih memiliki data penduduk
            // if ($item->penduduk()->exists()) {
            //     return back()->with('error', 'Gagal menghapus! RT ini masih memiliki data penduduk terkait.');
            // }
            $item->delete();
            $message = 'Data RT berhasil dihapus!';

        } else {
            return back()->with('error', 'Tipe data tidak valid.');
        }

        return redirect()->route('admin.rt-rw.index')->with('success', $message);
    }
}
