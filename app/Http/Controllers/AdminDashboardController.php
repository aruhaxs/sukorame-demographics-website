<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penduduk;
use App\Models\Perangkat;
use App\Models\Komoditas;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama dengan data ringkasan dan grafik.
     */
    public function index(): View
    {
        // Data untuk Kartu Ringkasan
        $data = [
            'total_penduduk' => Penduduk::count(),
            'total_perangkat' => Perangkat::count(),
            'total_komoditas' => Komoditas::count(),
        ];

        // Data untuk Grafik Jenis Kelamin (Pie Chart)
        $genderData = Penduduk::select(
                DB::raw('jenis_kelamin as gender'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('gender')
            ->get();
        
        $genderChartData = [
            'labels' => $genderData->pluck('gender'),
            'data' => $genderData->pluck('total'),
        ];

        // Data untuk Grafik Kelompok Usia (Bar Chart)
        $ageData = Penduduk::select(
                DB::raw("CASE 
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 0 AND 10 THEN 'Anak-anak'
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 11 AND 20 THEN 'Remaja'
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 21 AND 50 THEN 'Dewasa'
                    ELSE 'Lansia' 
                END as age_group"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('age_group')
            ->orderByRaw("FIELD(age_group, 'Anak-anak', 'Remaja', 'Dewasa', 'Lansia')")
            ->get();
            
        $ageChartData = [
            'labels' => $ageData->pluck('age_group'),
            'data' => $ageData->pluck('total'),
        ];

        // Kirim semua data yang dibutuhkan (ringkasan dan grafik) ke view
        return view('admin.dashboard', compact('data', 'genderChartData', 'ageChartData'));
    }

    // =================================================================
    // MANAJEMEN PENDUDUK
    // =================================================================

    /**
     * Menampilkan halaman daftar penduduk beserta data statistik.
     */
    public function indexPenduduk(): View
    {
        $totalPenduduk = Penduduk::count();
        $penduduks = Penduduk::orderBy('created_at', 'desc')->paginate(10);
        $totalKK = Penduduk::whereNotNull('nomor_kk')->distinct('nomor_kk')->count('nomor_kk');

        return view('admin.data_penduduk_index', compact('penduduks', 'totalPenduduk', 'totalKK'));
    }

    /**
     * Menampilkan form untuk menambah data penduduk baru.
     */
    public function createPenduduk(): View
    {
        return view('admin.input_penduduk');
    }

    /**
     * Memvalidasi dan menyimpan data penduduk baru ke database.
     */
    public function storePenduduk(Request $request): RedirectResponse
    {
        $validatedData = $request->validate($this->pendudukValidationRules());

        try {
            Penduduk::create($validatedData);
            return redirect()->route('admin.penduduk.index')->with('success', 'Data Penduduk berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan data.');
        }
    }

    /**
     * Menampilkan form untuk mengedit data penduduk yang ada.
     */
    public function editPenduduk(Penduduk $penduduk): View
    {
        return view('admin.edit_penduduk', compact('penduduk'));
    }

    /**
     * Memvalidasi dan menyimpan perubahan data penduduk ke database.
     */
    public function updatePenduduk(Request $request, Penduduk $penduduk): RedirectResponse
    {
        $validatedData = $request->validate($this->pendudukValidationRules($penduduk->id));

        try {
            $penduduk->update($validatedData);
            return redirect()->route('admin.penduduk.index')->with('success', 'Data Penduduk berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data.');
        }
    }

    /**
     * Menghapus data penduduk dari database.
     */
    public function destroyPenduduk(Penduduk $penduduk): RedirectResponse
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
    private function pendudukValidationRules(int $ignoreId = null): array
    {
        $nikRule = 'required|string|unique:penduduks,nik|max:16|min:16';

        if ($ignoreId) {
            $nikRule = ['required', 'string', 'max:16', 'min:16', Rule::unique('penduduks', 'nik')->ignore($ignoreId)];
        }

        return [
            'nama_lengkap' => 'required|string|max:255',
            'nik' => $nikRule,
            'nomor_kk' => 'nullable|string|max:16|min:16',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan',
            'rt' => 'required|string|max:3',
            'rw' => 'required|string|max:3',
            'pekerjaan' => 'nullable|string|max:255',
            'pendidikan_terakhir' => 'nullable|string|max:255',
            'agama' => 'nullable|string|max:50',
        ];
    }


    // =================================================================
    // MANAJEMEN PERANGKAT DESA
    // =================================================================

    /**
     * Menampilkan halaman daftar perangkat desa (RT/RW).
     */
    public function indexPerangkat(): View
    {
        $perangkats = Perangkat::orderBy('jabatan')->get();
        $jumlahPendudukPerWilayah = Penduduk::select('rt', 'rw', DB::raw('count(*) as total'))
            ->groupBy('rt', 'rw')
            ->get()->keyBy(fn ($item) => trim($item->rt) . '/' . trim($item->rw));

        foreach ($perangkats as $perangkat) {
            preg_match('/(\d+)\/(\d+)/', $perangkat->jabatan, $matches);
            $rt_rw_key = !empty($matches) ? trim($matches[1]) . '/' . trim($matches[2]) : null;
            $perangkat->jumlah_penduduk = $jumlahPendudukPerWilayah[$rt_rw_key]->total ?? 0;
        }

        $totalRT = $perangkats->filter(fn($p) => str_contains(strtoupper($p->jabatan), 'RT'))->count();
        $totalRW = $perangkats->filter(fn($p) => str_contains(strtoupper($p->jabatan), 'RW'))->count();

        return view('admin.data_perangkat_index', compact('perangkats', 'totalRT', 'totalRW'));
    }

    /**
     * Menampilkan form untuk menambah data perangkat desa baru.
     */
    public function createPerangkat(): View
    {
        return view('admin.input_perangkat');
    }

    /**
     * Memvalidasi dan menyimpan data perangkat desa baru.
     */
    public function storePerangkat(Request $request): RedirectResponse
    {
        $validatedData = $request->validate($this->perangkatValidationRules());
        try {
            Perangkat::create($validatedData);
            return redirect()->route('admin.perangkat.index')->with('success', 'Data Perangkat berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan data.');
        }
    }

    /**
     * Menampilkan form untuk mengedit data perangkat desa.
     */
    public function editPerangkat(Perangkat $perangkat): View
    {
        return view('admin.edit_perangkat', compact('perangkat'));
    }

    /**
     * Memvalidasi dan menyimpan perubahan data perangkat desa.
     */
    public function updatePerangkat(Request $request, Perangkat $perangkat): RedirectResponse
    {
        $validatedData = $request->validate($this->perangkatValidationRules());
        try {
            $perangkat->update($validatedData);
            return redirect()->route('admin.perangkat.index')->with('success', 'Data Perangkat berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data.');
        }
    }

    /**
     * Menghapus data perangkat desa dari database.
     */
    public function destroyPerangkat(Perangkat $perangkat): RedirectResponse
    {
        try {
            $perangkat->delete();
            return redirect()->route('admin.perangkat.index')->with('success', 'Data perangkat berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data.');
        }
    }
    
    /**
     * Aturan validasi untuk data perangkat desa.
     */
    private function perangkatValidationRules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:15',
            'wilayah' => 'nullable|string|max:255',
        ];
    }


    // =================================================================
    // MANAJEMEN KOMODITAS
    // =================================================================

    /**
     * Menampilkan halaman daftar komoditas.
     */
    public function indexKomoditas(): View
    {
        $komoditas = Komoditas::orderBy('nama_komoditas')->get();
        $totalKomoditas = $komoditas->count();
        $totalKategori = $komoditas->pluck('kategori')->unique()->count();

        return view('admin.data_komoditas_index', compact('komoditas', 'totalKomoditas', 'totalKategori'));
    }

    /**
     * Menampilkan form untuk menambah data komoditas baru.
     */
    public function createKomoditas(): View
    {
        return view('admin.input_komoditas');
    }

    /**
     * Memvalidasi dan menyimpan data komoditas baru.
     */
    public function storeKomoditas(Request $request): RedirectResponse
    {
        $validatedData = $request->validate($this->komoditasValidationRules());
        try {
            Komoditas::create($validatedData);
            return redirect()->route('admin.komoditas.index')->with('success', 'Data Komoditas berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan data komoditas.');
        }
    }

    /**
     * Menampilkan form untuk mengedit data komoditas.
     */
    public function editKomoditas(Komoditas $komoditas): View
    {
        return view('admin.edit_komoditas', compact('komoditas'));
    }

    /**
     * Memvalidasi dan menyimpan perubahan data komoditas.
     */
    public function updateKomoditas(Request $request, Komoditas $komoditas): RedirectResponse
    {
        $validatedData = $request->validate($this->komoditasValidationRules());
        try {
            $komoditas->update($validatedData);
            return redirect()->route('admin.komoditas.index')->with('success', 'Data Komoditas berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data komoditas.');
        }
    }

    /**
     * Menghapus data komoditas dari database.
     */
    public function destroyKomoditas(Komoditas $komoditas): RedirectResponse
    {
        try {
            $komoditas->delete();
            return redirect()->route('admin.komoditas.index')->with('success', 'Data komoditas berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data komoditas.');
        }
    }

    /**
     * Aturan validasi untuk data komoditas.
     */
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


    // =================================================================
    // MANAJEMEN BANGUNAN
    // =================================================================

    /**
     * Menampilkan halaman daftar bangunan (menggunakan data dummy).
     */
    public function indexBangunan(): View
    {
        // TODO: Ganti dengan implementasi data bangunan dari database.
        $bangunans = collect([
            (object)['id' => 1, 'nama' => 'SDN Sukorame', 'jenis' => 'Pendidikan', 'koordinat' => '-7.8, 112.5'],
            (object)['id' => 2, 'nama' => 'Puskesmas', 'jenis' => 'Kesehatan', 'koordinat' => '-7.8, 112.5'],
        ]);
        $stat = ['rt' => 12, 'rw' => 45, 'luas' => 8.2];

        return view('admin.data_bangunan_index', compact('bangunans', 'stat'));
    }
}