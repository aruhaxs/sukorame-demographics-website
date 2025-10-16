<?php

namespace App\Http\Controllers;

use App\Models\Komoditas;
use App\Models\Penduduk;
use App\Models\Perangkat;
use App\Models\Bangunan;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class AdminDashboardController extends Controller
{
    /**
     * =============================
     * DASHBOARD UTAMA
     * =============================
     */
    public function index(): View
    {
        $data = [
            'total_penduduk' => Penduduk::count(),
            'total_perangkat' => Perangkat::count(), // Anda bisa hapus jika tabel ini sudah tidak relevan
            'total_komoditas' => Komoditas::count(),
            'total_bangunan' => Bangunan::count(),
        ];
        $genderData = Penduduk::select('jenis_kelamin as gender', DB::raw('COUNT(*) as total'))->groupBy('gender')->get();
        $genderChartData = ['labels' => $genderData->pluck('gender')->map(fn($g) => $g ?? 'Tidak Diketahui'), 'data' => $genderData->pluck('total')];
        $ageData = Penduduk::select(DB::raw("CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 0 AND 10 THEN 'Anak-anak (0-10)' WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 11 AND 20 THEN 'Remaja (11-20)' WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 21 AND 50 THEN 'Dewasa (21-50)' ELSE 'Lansia (>50)' END as age_group"), DB::raw('COUNT(*) as total'))->groupBy('age_group')->orderByRaw("FIELD(age_group, 'Anak-anak (0-10)', 'Remaja (11-20)', 'Dewasa (21-50)', 'Lansia (>50)')")->get();
        $ageChartData = ['labels' => $ageData->pluck('age_group'), 'data' => $ageData->pluck('total')];
        return view('admin.dashboard', compact('data', 'genderChartData', 'ageChartData'));
    }

    /**
     * =============================
     * MANAJEMEN PENDUDUK
     * =============================
     */
    public function indexPenduduk(): View
    {
        $penduduks = Penduduk::with(['rw', 'rt'])->latest()->paginate(10);
        $totalPenduduk = Penduduk::count();
        $totalKK = Penduduk::distinct('nomor_kk')->count('nomor_kk');
        return view('admin.data_penduduk_index', compact('penduduks', 'totalPenduduk', 'totalKK'));
    }
    public function createPenduduk(): View
    {
        $rws = Rw::orderBy('nomor_rw')->get();
        return view('admin.input_penduduk', compact('rws'));
    }
    public function storePenduduk(Request $request): RedirectResponse
    {
        $validatedData = $request->validate($this->pendudukValidationRules());
        try {
            Penduduk::create($validatedData);
            return redirect()->route('admin.penduduk.index')->with('success', 'Data Penduduk berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan data. ' . $e->getMessage());
        }
    }
    public function editPenduduk(Penduduk $penduduk): View
    {
        $rws = Rw::orderBy('nomor_rw')->get();
        $penduduk->load(['rw', 'rt']);
        return view('admin.edit_penduduk', compact('penduduk', 'rws'));
    }
    public function updatePenduduk(Request $request, Penduduk $penduduk): RedirectResponse
    {
        $validatedData = $request->validate($this->pendudukValidationRules($penduduk->id));
        try {
            $penduduk->update($validatedData);
            return redirect()->route('admin.penduduk.index')->with('success', 'Data Penduduk berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data. ' . $e->getMessage());
        }
    }
    public function destroyPenduduk(Penduduk $penduduk): RedirectResponse
    {
        try {
            $penduduk->delete();
            return redirect()->route('admin.penduduk.index')->with('success', 'Data penduduk berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data.');
        }
    }
    private function pendudukValidationRules(int $ignoreId = null): array
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

    /**
     * =============================
     * API UNTUK DROPDOWN DINAMIS RT
     * =============================
     */
    public function getRtByRw($rw_id)
    {
        $rts = Rt::where('rw_id', $rw_id)->orderBy('nomor_rt', 'asc')->get(['id', 'nomor_rt']);
        return response()->json($rts);
    }

    /**
     * =============================
     * MANAJEMEN RT & RW
     * =============================
     */
    public function indexRtRw(): View
    {
        $rws = Rw::orderBy('nomor_rw', 'asc')->get();
        $rts = Rt::with('rw')->orderBy('nomor_rt', 'asc')->get();
        $totalRT = $rts->count();
        $totalRW = $rws->count();
        return view('admin.data_perangkat_index', compact('rws', 'rts', 'totalRT', 'totalRW'));
    }
    public function createRtRw(): View
    {
        $rws = Rw::orderBy('nomor_rw', 'asc')->get();
        return view('admin.input_perangkat', compact('rws'));
    }
    public function storeRtRw(Request $request): RedirectResponse
    {
        if ($request->input('type') === 'rw') {
            $validated = $request->validate(['nomor_rw' => 'required|integer|unique:rw,nomor_rw', 'ketua_rw' => 'required|string|max:255']);
            Rw::create($validated);
            $message = 'Data RW berhasil ditambahkan!';
        } elseif ($request->input('type') === 'rt') {
            $validated = $request->validate(['rw_id' => 'required|exists:rw,id', 'nomor_rt' => 'required|integer|unique:rt,nomor_rt,NULL,id,rw_id,' . $request->rw_id, 'ketua_rt' => 'required|string|max:255'], ['nomor_rt.unique' => 'Nomor RT sudah ada di dalam RW yang dipilih.']);
            Rt::create($validated);
            $message = 'Data RT berhasil ditambahkan!';
        } else {
            return back()->with('error', 'Tipe data tidak valid.');
        }
        return redirect()->route('admin.rt-rw.index')->with('success', $message);
    }
    public function editRtRw(string $type, int $id): View
    {
        if ($type === 'rw') $item = Rw::findOrFail($id);
        elseif ($type === 'rt') $item = Rt::findOrFail($id);
        else abort(404);
        $rws = Rw::orderBy('nomor_rw', 'asc')->get();
        return view('admin.edit_perangkat', compact('item', 'type', 'rws'));
    }
    public function updateRtRw(Request $request, string $type, int $id): RedirectResponse
    {
        if ($type === 'rw') {
            $item = Rw::findOrFail($id);
            $validated = $request->validate(['nomor_rw' => 'required|integer|unique:rw,nomor_rw,' . $id, 'ketua_rw' => 'required|string|max:255']);
            $item->update($validated);
            $message = 'Data RW berhasil diperbarui!';
        } elseif ($type === 'rt') {
            $item = Rt::findOrFail($id);
            $validated = $request->validate(['rw_id' => 'required|exists:rw,id', 'nomor_rt' => 'required|integer|unique:rt,nomor_rt,' . $id . ',id,rw_id,' . $request->rw_id, 'ketua_rt' => 'required|string|max:255'], ['nomor_rt.unique' => 'Nomor RT sudah ada di dalam RW yang dipilih.']);
            $item->update($validated);
            $message = 'Data RT berhasil diperbarui!';
        } else {
            return back()->with('error', 'Tipe data tidak valid.');
        }
        return redirect()->route('admin.rt-rw.index')->with('success', $message);
    }
    public function destroyRtRw(string $type, int $id): RedirectResponse
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
            $item->delete();
            $message = 'Data RT berhasil dihapus!';
        } else {
            return back()->with('error', 'Tipe data tidak valid.');
        }
        return redirect()->route('admin.rt-rw.index')->with('success', $message);
    }

    /**
     * =============================
     * MANAJEMEN KOMODITAS
     * =============================
     */
    public function indexKomoditas(): View
    {
        $komoditas = Komoditas::orderBy('nama_komoditas')->get();
        $totalKomoditas = $komoditas->count();
        $totalKategori = $komoditas->pluck('kategori')->unique()->count();
        return view('admin.data_komoditas_index', compact('komoditas', 'totalKomoditas', 'totalKategori'));
    }
    public function createKomoditas(): View
    {
        return view('admin.input_komoditas');
    }
    public function storeKomoditas(Request $request): RedirectResponse
    {
        $validatedData = $request->validate($this->komoditasValidationRules());
        if (isset($validatedData['harga'])) {
            $validatedData['harga'] = preg_replace('/[^0-9]/', '', $validatedData['harga']);
        }
        Komoditas::create($validatedData);
        return redirect()->route('admin.komoditas.index')->with('success', 'Data Komoditas berhasil ditambahkan!');
    }
    public function editKomoditas(Komoditas $komoditas): View
    {
        return view('admin.edit_komoditas', compact('komoditas'));
    }
    public function updateKomoditas(Request $request, Komoditas $komoditas): RedirectResponse
    {
        $validatedData = $request->validate($this->komoditasValidationRules());
        if (isset($validatedData['harga'])) {
            $validatedData['harga'] = preg_replace('/[^0-9]/', '', $validatedData['harga']);
        }
        $komoditas->update($validatedData);
        return redirect()->route('admin.komoditas.index')->with('success', 'Data Komoditas berhasil diperbarui!');
    }
    public function destroyKomoditas(Komoditas $komoditas): RedirectResponse
    {
        $komoditas->delete();
        return redirect()->route('admin.komoditas.index')->with('success', 'Data komoditas berhasil dihapus!');
    }
    private function komoditasValidationRules(): array
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

    /**
     * =============================
     * CRUD BANGUNAN
     * =============================
     */
    public function indexBangunan(): View
    {
        // Data untuk peta
        $bangunansForMap = Bangunan::with('rw', 'rt')->get();
        
        // Data untuk tabel dengan paginasi
        $bangunansForTable = Bangunan::with('rw', 'rt')->latest()->paginate(10);
        
        // Data untuk ringkasan
        $categoryCounts = Bangunan::select('kategori', DB::raw('COUNT(*) as total'))->groupBy('kategori')->pluck('total', 'kategori');
        
        return view('admin.data_bangunan_index', [
            'bangunans' => $bangunansForTable,
            'bangunansForMap' => $bangunansForMap,
            'categoryCounts' => $categoryCounts,
            'totalBangunan' => Bangunan::count()
        ]);
    }
    
    public function createBangunan(): View
    {
        $rws = Rw::orderBy('nomor_rw', 'asc')->get();
        return view('admin.input_bangunan', compact('rws'));
    }
    public function storeBangunan(Request $request): RedirectResponse
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
    public function editBangunan(Bangunan $bangunan): View
    {
        $rws = Rw::orderBy('nomor_rw', 'asc')->get();
        $rts = Rt::where('rw_id', $bangunan->rw_id)->orderBy('nomor_rt', 'asc')->get();
        return view('admin.edit_bangunan', compact('bangunan', 'rws', 'rts'));
    }
    public function updateBangunan(Request $request, Bangunan $bangunan): RedirectResponse
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
            if ($bangunan->foto) {
                Storage::disk('public')->delete($bangunan->foto);
            }
            $path = $request->file('foto')->store('bangunan_fotos', 'public');
            $validatedData['foto'] = $path;
        }
        $bangunan->update($validatedData);
        return redirect()->route('admin.bangunan.index')->with('success', 'Data Bangunan berhasil diperbarui!');
    }
    public function destroyBangunan(Bangunan $bangunan): RedirectResponse
    {
        if ($bangunan->foto) {
            Storage::disk('public')->delete($bangunan->foto);
        }
        $bangunan->delete();
        return redirect()->route('admin.bangunan.index')->with('success', 'Data Bangunan berhasil dihapus!');
    }
}