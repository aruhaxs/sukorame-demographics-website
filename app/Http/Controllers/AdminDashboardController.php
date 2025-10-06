<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penduduk;
use App\Models\Perangkat;
use App\Models\Komoditas; // Pastikan model ini ada
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama (ringkasan).
     */
    public function index()
    {
        $data = [
            'total_penduduk' => Penduduk::count(),
            'total_perangkat' => Perangkat::count(),
            'total_komoditas' => Komoditas::count(),
        ];
        return view('admin.dashboard', compact('data'));
    }

    // =================================================================
    // HALAMAN INDEX INDIVIDUAL (UNTUK SETIAP MENU)
    // =================================================================

    public function indexPenduduk()
    {
        $penduduks = Penduduk::orderBy('created_at', 'desc')->paginate(10);
        $totalPenduduk = Penduduk::count();
        $totalKK = Penduduk::whereNotNull('nomor_kk')->distinct('nomor_kk')->count('nomor_kk');
        return view('admin.data_penduduk_index', compact('penduduks', 'totalPenduduk', 'totalKK'));
    }

    public function indexPerangkat()
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

    public function indexKomoditas()
    {
        $komoditas = Komoditas::orderBy('nama_komoditas')->get();
        $totalKomoditas = $komoditas->count();
        $totalKategori = $komoditas->pluck('kategori')->unique()->count();
        return view('admin.data_komoditas_index', compact('komoditas', 'totalKomoditas', 'totalKategori'));
    }

    public function indexBangunan()
    {
        // Data Dummy untuk Bangunan
        $bangunans = collect([
            (object)['id' => 1, 'nama' => 'SDN Sukorame', 'jenis' => 'Pendidikan', 'koordinat' => '-7.8, 112.5'],
            (object)['id' => 2, 'nama' => 'Puskesmas', 'jenis' => 'Kesehatan', 'koordinat' => '-7.8, 112.5'],
        ]);
        $stat = ['rt' => 12, 'rw' => 45, 'luas' => 8.2];
        return view('admin.data_bangunan_index', compact('bangunans', 'stat'));
    }

    // =================================================================
    // FUNGSI CRUD (Create, Read, Update, Delete)
    // =================================================================

    // --- CRUD PENDUDUK ---
    public function createPenduduk() { return view('admin.input_penduduk'); }
    public function storePenduduk(Request $request) {
        $validatedData = $request->validate([
            'nama_lengkap' => 'required|string|max:255','nik' => 'required|string|unique:penduduks,nik|max:16|min:16','nomor_kk' => 'nullable|string|max:16|min:16','tanggal_lahir' => 'required|date','jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan','rt' => 'required|string|max:3','rw' => 'required|string|max:3','pekerjaan' => 'nullable|string|max:255','pendidikan_terakhir' => 'nullable|string|max:255','agama' => 'nullable|string|max:50',
        ]);
        try { Penduduk::create($validatedData); return redirect()->route('admin.penduduk.index')->with('success', 'Data Penduduk berhasil ditambahkan!'); } catch (\Exception $e) { return back()->withInput()->with('error', 'Gagal menyimpan data.'); }
    }
    public function editPenduduk(Penduduk $penduduk) { return view('admin.edit_penduduk', compact('penduduk')); }
    public function updatePenduduk(Request $request, Penduduk $penduduk) {
        $validatedData = $request->validate([
            'nama_lengkap' => 'required|string|max:255','nik' => ['required','string','max:16','min:16',Rule::unique('penduduks', 'nik')->ignore($penduduk->id)],'nomor_kk' => 'nullable|string|max:16|min:16','tanggal_lahir' => 'required|date','jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan','rt' => 'required|string|max:3','rw' => 'required|string|max:3','pekerjaan' => 'nullable|string|max:255','pendidikan_terakhir' => 'nullable|string|max:255','agama' => 'nullable|string|max:50',
        ]);
        try { $penduduk->update($validatedData); return redirect()->route('admin.penduduk.index')->with('success', 'Data Penduduk berhasil diperbarui!'); } catch (\Exception $e) { return back()->withInput()->with('error', 'Gagal memperbarui data.'); }
    }
    public function destroyPenduduk(Penduduk $penduduk) {
        try { $penduduk->delete(); return redirect()->route('admin.penduduk.index')->with('success', 'Data penduduk berhasil dihapus!'); } catch (\Exception $e) { return back()->with('error', 'Gagal menghapus data.'); }
    }

    // --- CRUD PERANGKAT ---
    public function createPerangkat() { return view('admin.input_perangkat'); }
    public function storePerangkat(Request $request) {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255','jabatan' => 'required|string|max:255','nomor_telepon' => 'required|string|max:15','wilayah' => 'nullable|string|max:255',
        ]);
        try { Perangkat::create($validatedData); return redirect()->route('admin.perangkat.index')->with('success', 'Data Perangkat berhasil ditambahkan!'); } catch (\Exception $e) { return back()->withInput()->with('error', 'Gagal menyimpan data.'); }
    }
    public function editPerangkat(Perangkat $perangkat) { return view('admin.edit_perangkat', compact('perangkat')); }
    public function updatePerangkat(Request $request, Perangkat $perangkat) {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255','jabatan' => 'required|string|max:255','nomor_telepon' => 'required|string|max:15','wilayah' => 'nullable|string|max:255',
        ]);
        try { $perangkat->update($validatedData); return redirect()->route('admin.perangkat.index')->with('success', 'Data Perangkat berhasil diperbarui!'); } catch (\Exception $e) { return back()->withInput()->with('error', 'Gagal memperbarui data.'); }
    }
    public function destroyPerangkat(Perangkat $perangkat) {
        try { $perangkat->delete(); return redirect()->route('admin.perangkat.index')->with('success', 'Data perangkat berhasil dihapus!'); } catch (\Exception $e) { return back()->with('error', 'Gagal menghapus data.'); }
    }

    // --- CRUD KOMODITAS ---
    public function createKomoditas() { return view('admin.input_komoditas'); }
    public function storeKomoditas(Request $request) {
        $validatedData = $request->validate([
            'nama_komoditas' => 'required|string|max:255','kategori' => 'required|string|max:255','produksi' => 'nullable|string|max:255','periode' => 'nullable|string|max:255','produsen' => 'nullable|string|max:255','lokasi' => 'nullable|string|max:255','harga' => 'nullable|string|max:255',
        ]);
        try { Komoditas::create($validatedData); return redirect()->route('admin.komoditas.index')->with('success', 'Data Komoditas berhasil ditambahkan!'); } catch (\Exception $e) { return back()->withInput()->with('error', 'Gagal menyimpan data komoditas.'); }
    }
    public function editKomoditas(Komoditas $komoditas) { return view('admin.edit_komoditas', compact('komoditas')); }
    public function updateKomoditas(Request $request, Komoditas $komoditas) {
        $validatedData = $request->validate([
            'nama_komoditas' => 'required|string|max:255','kategori' => 'required|string|max:255','produksi' => 'nullable|string|max:255','periode' => 'nullable|string|max:255','produsen' => 'nullable|string|max:255','lokasi' => 'nullable|string|max:255','harga' => 'nullable|string|max:255',
        ]);
        try { $komoditas->update($validatedData); return redirect()->route('admin.komoditas.index')->with('success', 'Data Komoditas berhasil diperbarui!'); } catch (\Exception $e) { return back()->withInput()->with('error', 'Gagal memperbarui data komoditas.'); }
    }
    public function destroyKomoditas(Komoditas $komoditas) {
        try { $komoditas->delete(); return redirect()->route('admin.komoditas.index')->with('success', 'Data komoditas berhasil dihapus!'); } catch (\Exception $e) { return back()->with('error', 'Gagal menghapus data komoditas.'); }
    }
}
