<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penduduk;
use App\Models\Rw;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PendudukController extends Controller
{
    /**
     * Menampilkan daftar penduduk.
     */
    public function index(): View
    {
        $penduduks = Penduduk::with(['rw', 'rt'])->latest()->paginate(10);
        $totalPenduduk = Penduduk::count();
        $totalKK = Penduduk::distinct('nomor_kk')->count('nomor_kk');
        return view('admin.data_penduduk_index', compact('penduduks', 'totalPenduduk', 'totalKK'));
    }

    /**
     * Menampilkan form untuk menambah penduduk baru.
     */
    public function create(): View
    {
        $rws = Rw::orderBy('nomor_rw')->get();
        return view('admin.input_penduduk', compact('rws'));
    }

    /**
     * Menyimpan data penduduk baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate($this->validationRules());

        try {
            Penduduk::create($validatedData);
            return redirect()->route('admin.penduduk.index')->with('success', 'Data Penduduk berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan data. ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form untuk mengedit data penduduk.
     */
    public function edit(Penduduk $penduduk): View
    {
        $rws = Rw::orderBy('nomor_rw')->get();
        $penduduk->load(['rw', 'rt']);
        return view('admin.edit_penduduk', compact('penduduk', 'rws'));
    }

    /**
     * Memperbarui data penduduk.
     */
    public function update(Request $request, Penduduk $penduduk): RedirectResponse
    {
        $validatedData = $request->validate($this->validationRules($penduduk->id));

        try {
            $penduduk->update($validatedData);
            return redirect()->route('admin.penduduk.index')->with('success', 'Data Penduduk berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data. ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data penduduk.
     */
    public function destroy(Penduduk $penduduk): RedirectResponse
    {
        try {
            $penduduk->delete();
            return redirect()->route('admin.penduduk.index')->with('success', 'Data penduduk berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data.');
        }
    }

    /**
     * Aturan validasi untuk data penduduk.
     */
    private function validationRules(int $ignoreId = null): array
    {
        $nikRule = ['required', 'string', 'digits:16', Rule::unique('penduduks', 'nik')->ignore($ignoreId)];

        return [
            'nama_lengkap' => 'required|string|max:255',
            'nik' => $nikRule,
            'nomor_kk' => 'nullable|string|digits:16',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan',
            'rw_id' => 'required|exists:rw,id',
            'rt_id' => 'required|exists:rt,id',
            'pekerjaan' => 'nullable|string|max:255',
            'pendidikan_terakhir' => 'nullable|string|max:255',
            'agama' => 'nullable|string|max:50',
        ];
    }
}
