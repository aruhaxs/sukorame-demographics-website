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
            'total_perangkat' => Perangkat::count(),
            'total_komoditas' => Komoditas::count(),
            'total_bangunan' => Bangunan::count(),
        ];

        // Statistik Jenis Kelamin
        $genderData = Penduduk::select('jenis_kelamin as gender', DB::raw('COUNT(*) as total'))
            ->groupBy('gender')
            ->get();

        $genderChartData = [
            'labels' => $genderData->pluck('gender')->map(fn($g) => $g ?? 'Tidak Diketahui'),
            'data' => $genderData->pluck('total'),
        ];

        // Statistik Umur
        $ageData = Penduduk::select(
            DB::raw("CASE
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 0 AND 10 THEN 'Anak-anak (0-10)'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 11 AND 20 THEN 'Remaja (11-20)'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 21 AND 50 THEN 'Dewasa (21-50)'
                ELSE 'Lansia (>50)'
            END as age_group"),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('age_group')
            ->orderByRaw("FIELD(age_group, 'Anak-anak (0-10)', 'Remaja (11-20)', 'Dewasa (21-50)', 'Lansia (>50)')")
            ->get();

        $ageChartData = [
            'labels' => $ageData->pluck('age_group'),
            'data' => $ageData->pluck('total'),
        ];

        return view('admin.dashboard', compact('data', 'genderChartData', 'ageChartData'));
    }

    /**
     * =============================
     * MANAJEMEN PENDUDUK
     * =============================
     */
    public function indexPenduduk(): View
    {
        $penduduks = Penduduk::latest()->paginate(10);
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
        $selectedRw = Rw::find($penduduk->rw);
        $selectedRt = Rt::find($penduduk->rt);

        return view('admin.edit_penduduk', compact('penduduk', 'rws', 'selectedRw', 'selectedRt'));
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
        $nikRule = [
            'required',
            'string',
            'digits:16',
            Rule::unique('penduduks', 'nik')->ignore($ignoreId),
        ];

        return [
            'nama_lengkap' => 'required|string|max:255',
            'nik' => $nikRule,
            'nomor_kk' => 'nullable|string|digits:16',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan',
            'rw' => 'required|exists:rws,id',
            'rt' => 'required|exists:rts,id',
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
        $rts = Rt::where('rw_id', $rw_id)
            ->orderBy('nomor_rt', 'asc')
            ->get(['id', 'nomor_rt']);

        return response()->json($rts);
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

        // FIX: Membersihkan input 'harga', menghapus semua karakter selain angka
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

        // FIX: Membersihkan input 'harga', menghapus semua karakter selain angka
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
            // Validasi harga sebagai string untuk menerima format apapun, pembersihan dilakukan di controller
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
        $bangunans = Bangunan::latest()->paginate(10);
        $totalBangunan = Bangunan::count();
        return view('admin.data_bangunan_index', compact('bangunans', 'totalBangunan'));
    }

    public function createBangunan(): View
    {
        // Kirim data RW dan RT untuk dropdown
        $rws = Rw::orderBy('nomor_rw', 'asc')->get();
        $rts = Rt::orderBy('nomor_rt', 'asc')->get();
        return view('admin.input_bangunan', compact('rws', 'rts'));
    }

    public function storeBangunan(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'nama_bangunan' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'rw_id' => 'nullable|exists:rws,id',
            'rt_id' => 'nullable|exists:rts,id',
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
        $rts = Rt::orderBy('nomor_rt', 'asc')->get();
        return view('', compact('bangunan', 'rws', 'rts'));
    }

    public function updateBangunan(Request $request, Bangunan $bangunan): RedirectResponse
    {
        $validatedData = $request->validate([
            'nama_bangunan' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'rw_id' => 'nullable|exists:rws,id',
            'rt_id' => 'nullable|exists:rts,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('bangunan_fotos', 'public');
            $validatedData['foto'] = $path;
        }

        $bangunan->update($validatedData);
        return redirect()->route('admin.bangunan.index')->with('success', 'Data Bangunan berhasil diperbarui!');
    }

    public function destroyBangunan(Bangunan $bangunan): RedirectResponse
    {
        $bangunan->delete();
        return redirect()->route('admin.bangunan.index')->with('success', 'Data Bangunan berhasil dihapus!');
    }
}