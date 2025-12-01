<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class DemografiController extends Controller
{
    public function index(Request $request)
    {
        $firebaseUrl = env('FIREBASE_DATABASE_URL');
        $firebaseSecret = env('FIREBASE_DB_SECRET');

        if (!str_ends_with($firebaseUrl, '/')) {
            $firebaseUrl .= '/';
        }

        // --- 1. AMBIL DATA DARI FIREBASE ---
        $response = Http::get($firebaseUrl . 'penduduks.json', [
            'auth' => $firebaseSecret
        ]);

        $rawData = $response->json() ?? [];

        // --- 2. NORMALISASI DATA ---
        // Mengubah array menjadi Collection Object & menormalisasi teks agar perhitungan akurat
        $pendudukData = collect($rawData)->map(function ($item) {
            $obj = (object) $item;
            
            // Buat properti lowercase untuk memudahkan filter (mengatasi Laki-Laki vs Laki-laki)
            $obj->gender_norm = strtolower($obj->jenisKelamin ?? $obj->jenis_kelamin ?? '');
            $obj->status_norm = strtolower($obj->statusDiKeluarga ?? $obj->status_keluarga ?? '');
            
            // Pastikan field tanggal lahir ada
            $obj->tgl_lahir_fix = $obj->tanggalLahir ?? $obj->tanggal_lahir ?? null;

            return $obj;
        });

        // --- 3. PERHITUNGAN DATA DASAR ---
        $totalPenduduk = $pendudukData->count();

        // Hitung Gender (Case Insensitive)
        $jumlahLakiLaki = $pendudukData->where('gender_norm', 'laki-laki')->count();
        $jumlahPerempuan = $pendudukData->where('gender_norm', 'perempuan')->count();

        // Hitung KK (Mencari kata "kepala keluarga" dalam string status)
        $jumlahKK = $pendudukData->filter(function ($item) {
            return str_contains($item->status_norm, 'kepala keluarga');
        })->count();

        // --- 4. PEMROSESAN DATA CHART (PIE CHART) ---
        // Helper untuk grouping data chart
        $processData = function ($data, $field) {
            $counts = $data->groupBy(function ($item) use ($field) {
                return $item->$field ?? 'Lainnya'; 
            })->map(fn ($item) => $item->count());

            return [
                'labels' => $counts->keys()->toArray(),
                'counts' => $counts->values()->toArray(),
            ];
        };

        // Buat koleksi khusus chart dengan field asli (untuk label yang rapi)
        $chartCollection = $pendudukData->map(function($item) {
            return (object) [
                'agama' => $item->agama ?? 'Lainnya',
                'gender' => $item->jenisKelamin ?? $item->jenis_kelamin ?? 'Lainnya',
                'pendidikan' => $item->pendidikanTerakhir ?? $item->pendidikan_terakhir ?? 'Lainnya',
                'pekerjaan' => $item->pekerjaan ?? 'Lainnya'
            ];
        });

        $allDemografiData = [
            'agama'      => $processData($chartCollection, 'agama'),
            'gender'     => $processData($chartCollection, 'gender'),
            'pendidikan' => $processData($chartCollection, 'pendidikan'),
            'pekerjaan'  => $processData($chartCollection, 'pekerjaan'),
        ];

        // List Agama untuk view
        $agamaDataList = $pendudukData->groupBy('agama')->map(fn ($item) => $item->count());

        // --- 5. DATA BAR CHART (USIA) ---
        // Menggunakan fungsi khusus dengan logika Carbon yang diperbaiki
        $chartData = $this->processAgeData($pendudukData);

        return view('demografi', [
            'totalPenduduk'    => $totalPenduduk,
            'jumlahLakiLaki'   => $jumlahLakiLaki,
            'jumlahPerempuan'  => $jumlahPerempuan,
            'jumlahKK'         => $jumlahKK,
            'agamaData'        => $agamaDataList,
            'chartData'        => $chartData,
            'allDemografiData' => $allDemografiData,
        ]);
    }

    private function processAgeData($pendudukData)
    {
        // Kategori Rentang Usia (Standar Demografi 5 Tahun)
        $ageRanges = [
            '0-4', '5-9', '10-14', '15-19', '20-24', '25-29', '30-34', '35-39', '40-44', '45-49',
            '50-54', '55-59', '60-64', '65-69', '70-74', '75-79', '80-84', '85+'
        ];

        $dataCounts = array_fill(0, count($ageRanges), 0);
        
        // PENTING: Gunakan Timezone Asia/Jakarta (Sesuai Referensi DashboardController)
        $now = Carbon::now('Asia/Jakarta');

        foreach ($pendudukData as $penduduk) {
            // Gunakan field yang sudah dinormalisasi di atas
            if (empty($penduduk->tgl_lahir_fix)) {
                continue; 
            }

            try { 
                // LOGIKA REFERENSI:
                // 1. Parse Tanggal Lahir
                $birthDate = Carbon::parse($penduduk->tgl_lahir_fix);
                
                // 2. Hitung selisih tahun dengan Waktu Sekarang (WIB)
                $age = $birthDate->diffInYears($now); 
                
                // 3. Tentukan Index Array (umur dibagi 5, dibulatkan ke bawah)
                // Contoh: Umur 23 / 5 = 4.6 -> floor jadi 4. Index 4 adalah "20-24"
                $ageIndex = floor($age / 5);

                // Validasi Index agar tidak error array out of bounds
                if ($ageIndex < 0) continue;
                if ($ageIndex >= count($ageRanges)) $ageIndex = count($ageRanges) - 1;

                $dataCounts[$ageIndex]++;
                
            } catch (\Exception $e) { 
                continue; 
            }
        }

        return [ 'labels' => $ageRanges, 'total' => $dataCounts ];
    }
}