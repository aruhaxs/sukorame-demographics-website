<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penduduk;
use App\Models\Rw;
use App\Models\Rt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;

class PendudukController extends Controller
{
    public function __construct()
    {
        // Controller ini 100% aman dimuat
    }
    
    public function index(): View
    {
        $penduduks = Penduduk::with(['rw', 'rt'])->latest()->paginate(10);
        $totalPenduduk = Penduduk::count(); // Ambil dari MySQL
        $totalKK = Penduduk::distinct('nomor_kk')->whereNotNull('nomor_kk')->count('nomor_kk');
        
        return view('admin.data_penduduk_index', compact('penduduks', 'totalPenduduk', 'totalKK'));
    }

    public function create(): View
    {
        $rws = Rw::orderBy('nomor_rw')->get();
        // Menambahkan kodepos ke default values
        $defaultValues = [
            'kelurahan' => 'Sukorame',
            'kecamatan' => 'Mojoroto',
            'kabupaten' => 'Kota Kediri',
            'provinsi' => 'Jawa Timur',
            'kewarganegaraan' => 'Indonesia',
            'kodepos' => '64119', 
        ];

        return view('admin.input_penduduk', compact('rws', 'defaultValues'));
    }

    /**
     * Helper untuk menentukan path penyimpanan file.
     * Struktur: dokumen/kk/<nomor_kk>/<nik>/<jenis_dokumen>/
     * Jika nomor_kk null, struktur: dokumen/nik/<nik>/<jenis_dokumen>/
     */
    private function getStoragePath(string $nik, ?string $nomorKK): string
    {
        // Bersihkan data untuk memastikan aman sebagai nama folder
        $cleanNik = str_replace(['/', '\\'], '', $nik);
        $cleanKK = str_replace(['/', '\\'], '', $nomorKK);

        if ($cleanKK) {
            // Path berdasarkan Nomor KK: dokumen/kk/<nomor_kk>/<nik>/
            return "dokumen/kk/{$cleanKK}/{$cleanNik}";
        }
        
        // Path berdasarkan NIK saja: dokumen/nik/<nik>/
        return "dokumen/nik/{$cleanNik}";
    }


    public function store(Request $request): RedirectResponse
    {
        // Validasi data input, termasuk file
        $validatedData = $request->validate($this->validationRules());

        // Parsing tanggal lahir
        try {
            $validatedData['tanggal_lahir'] = Carbon::parse($validatedData['tanggal_lahir'])->format('Y-m-d');
            $tanggalLahirFirestore = Carbon::parse($validatedData['tanggal_lahir'])->format('d-MM-Y');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Format Tanggal Lahir tidak valid.');
        }

        $penduduk = null;
        $pathKTP = null; 
        $pathKK = null;
        $nik = $validatedData['nik'];
        $nomorKK = $validatedData['nomor_kk'];

        // Tentukan base path penyimpanan
        $basePath = $this->getStoragePath($nik, $nomorKK);

        try {
            $rt = Rt::find($validatedData['rt_id']);
            $rw = Rw::find($validatedData['rw_id']);

            // --- 1. PROSES UPLOAD FILE ---

            // KTP
            if ($request->hasFile('foto_ktp_url')) {
                Log::info("[KTP Upload] File diterima. Base Path: {$basePath}");
                // Simpan file di folder: <basePath>/ktp
                $pathKTP = $request->file('foto_ktp_url')->store("{$basePath}/ktp", 'public');
                $validatedData['foto_ktp_url'] = $pathKTP; 
            } else {
                 Log::warning('[KTP Upload] File TIDAK ditemukan di request.');
                 $validatedData['foto_ktp_url'] = null;
            }

            // KK
            if ($request->hasFile('foto_kk_url')) {
                Log::info("[KK Upload] File diterima. Base Path: {$basePath}");
                // Simpan file di folder: <basePath>/kk
                $pathKK = $request->file('foto_kk_url')->store("{$basePath}/kk", 'public');
                $validatedData['foto_kk_url'] = $pathKK;
            } else {
                 Log::warning('[KK Upload] File TIDAK ditemukan di request.');
                 $validatedData['foto_kk_url'] = null;
            }
            
            // --- 2. SIMPAN KE MYSQL ---
            $penduduk = Penduduk::create($validatedData);

            // --- 3. TENTUKAN DATA UNTUK FIRESTORE (Payload) ---
            $firestoreData = [
                'nama_lengkap' => $validatedData['nama_lengkap'],
                'tanggal_lahir' => $tanggalLahirFirestore,
                'jenis_kelamin' => $validatedData['jenis_kelamin'],
                'alamat' => $this->combineAlamat($validatedData, $rt, $rw),
                'alamat_terstruktur' => [
                    'jalan' => $validatedData['jalan'] ?? '',
                    'rt' => $rt->nomor_rt ?? '',
                    'rw' => $rw->nomor_rw ?? '',
                    'kelurahan' => $validatedData['kelurahan'] ?? 'Sukorame',
                    'kecamatan' => $validatedData['kecamatan'] ?? 'Mojoroto',
                    'kabupaten' => $validatedData['kabupaten'] ?? 'Kota Kediri',
                    'provinsi' => $validatedData['provinsi'] ?? 'Jawa Timur',
                    'kodepos' => $validatedData['kodepos'] ?? '64119',
                ],
                'kk' => $validatedData['nomor_kk'] ?? null,
                'foto_ktp_url' => $validatedData['foto_ktp_url'] ? Storage::url($validatedData['foto_ktp_url']) : null,
                'foto_kk_url' => $validatedData['foto_kk_url'] ? Storage::url($validatedData['foto_kk_url']) : null,
            ];
            // --- LOGIKA SYNC FIRESTORE DIHILANGKAN ---

            return redirect()->route('admin.penduduk.index')->with('success', 'Data penduduk berhasil ditambahkan (MySQL).');

        } catch (\Exception $e) {
            Log::error('Error storing penduduk: ' . $e->getMessage());
            
            // Rollback: Hapus file jika DB save gagal
            if ($pathKTP && Storage::disk('public')->exists($pathKTP)) {
                 Storage::disk('public')->delete($pathKTP);
            }
            if ($pathKK && Storage::disk('public')->exists($pathKK)) {
                 Storage::disk('public')->delete($pathKK);
            }

            if (isset($penduduk)) $penduduk->delete();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }
    
    public function edit(Penduduk $penduduk): View
    {
        $rws = Rw::orderBy('nomor_rw')->get();
        $penduduk->load(['rw', 'rt']);
        $defaultValues = [
            'kelurahan' => $penduduk->kelurahan ?? 'Sukorame',
            'kecamatan' => $penduduk->kecamatan ?? 'Mojoroto',
            'kabupaten' => $penduduk->kabupaten ?? 'Kota Kediri',
            'provinsi' => $penduduk->provinsi ?? 'Jawa Timur',
            'kewarganegaraan' => $penduduk->kewarganegaraan ?? 'Indonesia',
            'kodepos' => $penduduk->kodepos ?? '64119', // DEFAULT KODEPOS BARU
        ];
        return view('admin.edit_penduduk', compact('penduduk', 'rws', 'defaultValues'));
    }

    public function update(Request $request, Penduduk $penduduk): RedirectResponse
    {
        // Validasi, pastikan NIK unik kecuali NIK milik penduduk ini sendiri
        $validatedData = $request->validate($this->validationRules($penduduk->nik));

        // Parsing tanggal lahir
        try {
            $validatedData['tanggal_lahir'] = Carbon::parse($validatedData['tanggal_lahir'])->format('Y-m-d');
            $tanggalLahirFirestore = Carbon::parse($validatedData['tanggal_lahir'])->format('d-MM-Y');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Format Tanggal Lahir tidak valid.');
        }

        // Ambil NIK dan Nomor KK saat ini untuk penentuan path
        $nik = $penduduk->nik; // NIK tidak berubah
        $nomorKKBaru = $validatedData['nomor_kk'];
        $basePathBaru = $this->getStoragePath($nik, $nomorKKBaru);
        
        // Ambil path lama (berdasarkan data lama)
        $nomorKKlama = $penduduk->nomor_kk;
        $basePathLama = $this->getStoragePath($nik, $nomorKKlama);

        try {
            $rt = Rt::find($validatedData['rt_id']);
            $rw = Rw::find($validatedData['rw_id']);
            
            // --- 1. PROSES UPLOAD FILE BARU & HAPUS FILE LAMA ---

            // KTP
            if ($request->hasFile('foto_ktp_url')) {
                // Hapus file lama jika ada
                if ($penduduk->foto_ktp_url && Storage::disk('public')->exists($penduduk->foto_ktp_url)) {
                    Storage::disk('public')->delete($penduduk->foto_ktp_url);
                }
                // Simpan file baru di path baru
                $pathKTP = $request->file('foto_ktp_url')->store("{$basePathBaru}/ktp", 'public');
                $validatedData['foto_ktp_url'] = $pathKTP; 
            } else {
                 // Jika tidak ada file baru diupload, pertahankan path file lama, tetapi 
                 // jika nomor KK berubah, file lama harus dipindahkan
                 $validatedData['foto_ktp_url'] = $this->handleFileRelocation($penduduk->foto_ktp_url, $basePathLama, $basePathBaru, 'ktp');
            }

            // KK
            if ($request->hasFile('foto_kk_url')) {
                // Hapus file lama jika ada
                if ($penduduk->foto_kk_url && Storage::disk('public')->exists($penduduk->foto_kk_url)) {
                    Storage::disk('public')->delete($penduduk->foto_kk_url);
                }
                // Simpan file baru di path baru
                $pathKK = $request->file('foto_kk_url')->store("{$basePathBaru}/kk", 'public');
                $validatedData['foto_kk_url'] = $pathKK;
            } else {
                // Jika tidak ada file baru diupload, pertahankan path file lama, tetapi 
                // jika nomor KK berubah, file lama harus dipindahkan
                $validatedData['foto_kk_url'] = $this->handleFileRelocation($penduduk->foto_kk_url, $basePathLama, $basePathBaru, 'kk');
            }
            
            // --- 2. UPDATE MYSQL ---
            $penduduk->update($validatedData); 
            
            $firestoreData = [
                'nama_lengkap' => $validatedData['nama_lengkap'],
                'tanggal_lahir' => $tanggalLahirFirestore,
                'jenis_kelamin' => $validatedData['jenis_kelamin'],
                'alamat' => $this->combineAlamat($validatedData, $rt, $rw),
                'alamat_terstruktur' => [
                    'jalan' => $validatedData['jalan'] ?? '',
                    'rt' => $rt->nomor_rt ?? '',
                    'rw' => $rw->nomor_rw ?? '',
                    'kelurahan' => $validatedData['kelurahan'] ?? 'Sukorame',
                    'kecamatan' => $validatedData['kecamatan'] ?? 'Mojoroto',
                    'kabupaten' => $validatedData['kabupaten'] ?? 'Kota Kediri',
                    'provinsi' => $validatedData['provinsi'] ?? 'Jawa Timur',
                    'kodepos' => $validatedData['kodepos'] ?? '64119',
                ],
                'kk' => $validatedData['nomor_kk'] ?? null,
                'foto_ktp_url' => $validatedData['foto_ktp_url'] ? Storage::url($validatedData['foto_ktp_url']) : null,
                'foto_kk_url' => $validatedData['foto_kk_url'] ? Storage::url($validatedData['foto_kk_url']) : null,
            ];

            // Catatan: Logika sync Firestore dihilangkan untuk mencegah crash.
            
            return redirect()->route('admin.penduduk.index')->with('success', 'Data penduduk berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Error updating penduduk: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Helper untuk memindahkan file jika path penyimpanan berubah.
     * Mengembalikan path baru jika dipindahkan, atau path lama jika tidak perlu dipindahkan.
     */
    private function handleFileRelocation(?string $oldPath, string $oldBasePath, string $newBasePath, string $fileType): ?string
    {
        if (!$oldPath) {
            return null; // Tidak ada file lama, kembalikan null
        }
        
        // Cek apakah base path berubah
        if ($oldBasePath !== $newBasePath) {
            $fileName = basename($oldPath);
            $newPath = "{$newBasePath}/{$fileType}/{$fileName}";

            if (Storage::disk('public')->exists($oldPath)) {
                // Lakukan pemindahan
                $success = Storage::disk('public')->move($oldPath, $newPath);
                if ($success) {
                    Log::info("File {$fileType} dipindahkan dari {$oldPath} ke {$newPath}");
                    return $newPath;
                } else {
                    Log::error("Gagal memindahkan file {$fileType} dari {$oldPath} ke {$newPath}");
                    return $oldPath; // Kembalikan path lama jika gagal move
                }
            }
        }
        
        return $oldPath; // Path tidak berubah, atau file tidak ada di storage (meskipun ada path di DB)
    }


    public function destroy(Penduduk $penduduk): RedirectResponse
    {
        $nik = $penduduk->nik;
        try {
            
            // Hapus file terkait dari storage sebelum menghapus record dari DB
            if ($penduduk->foto_ktp_url && Storage::disk('public')->exists($penduduk->foto_ktp_url)) {
                Storage::disk('public')->delete($penduduk->foto_ktp_url);
            }
            if ($penduduk->foto_kk_url && Storage::disk('public')->exists($penduduk->foto_kk_url)) {
                Storage::disk('public')->delete($penduduk->foto_kk_url);
            }
            
            $penduduk->delete(); // Hapus dari MySQL

            // Catatan: Logika hapus Firestore dihilangkan untuk mencegah crash.
            
            return redirect()->route('admin.penduduk.index')->with('success', 'Data penduduk berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting penduduk: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }


    private function validationRules(string $ignoreNik = null): array
    {
        $nikRule = ['required', 'string', 'digits:16', Rule::unique('penduduks', 'nik')->ignore($ignoreNik, 'nik')];

        return [
            'nik' => $nikRule,
            'nomor_kk' => 'nullable|string|digits:16',
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan',
            'agama' => 'nullable|string|max:50|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'pendidikan_terakhir' => 'nullable|string|max:50|in:Tidak Sekolah,SD,SMP,SMA/SMK,Diploma,S1,S2,S3',
            'pekerjaan' => 'nullable|string|max:100',
            'status_perkawinan' => 'nullable|string|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'kewarganegaraan' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20|regex:/^[0-9\+\-\(\)\s]*$/',
            'email' => 'nullable|email|max:255',
            'rw_id' => 'required|exists:rw,id', 
            'rt_id' => 'required|exists:rt,id', 
            'jalan' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kabupaten' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'kodepos' => 'nullable|string|max:10|regex:/^[0-9]*$/',
            
            // Aturan file yang paling ringan untuk mencegah masalah deteksi MIME
            'foto_ktp_url' => 'nullable|mimes:jpeg,png,jpg|max:10240', 
            'foto_kk_url' => 'nullable|mimes:jpeg,png,jpg|max:10240', 
        ];
    }

    private function combineAlamat(array $validatedData, ?Rt $rt, ?Rw $rw): string
    {
        return collect([
            $validatedData['jalan'] ?? null,
            ($rt ? 'RT ' . $rt->nomor_rt : null),
            ($rw ? 'RW ' . $rw->nomor_rw : null),
            $validatedData['kelurahan'] ?? null,
            $validatedData['kecamatan'] ?? null,
            $validatedData['kabupaten'] ?? null,
            $validatedData['provinsi'] ?? null,
            $validatedData['kodepos'] ?? null
        ])->filter(fn($value) => !is_null($value) && $value !== '')->implode(', ');
    }
}
