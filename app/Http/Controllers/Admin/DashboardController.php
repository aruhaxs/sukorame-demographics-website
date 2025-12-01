<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama dengan data Realtime Firebase.
     */
    public function index(): View
    {
        // --- 1. SETUP KONEKSI FIREBASE ---
        $firebaseUrl = env('FIREBASE_DATABASE_URL');
        $firebaseSecret = env('FIREBASE_DB_SECRET');

        if (!str_ends_with($firebaseUrl, '/')) {
            $firebaseUrl .= '/';
        }

        // --- 2. AMBIL DATA DARI FIREBASE ---
        $penduduksRaw  = Http::get($firebaseUrl . 'penduduks.json', ['auth' => $firebaseSecret])->json() ?? [];
        $rtsRaw        = Http::get($firebaseUrl . 'rts.json', ['auth' => $firebaseSecret])->json() ?? [];
        $rwsRaw        = Http::get($firebaseUrl . 'rws.json', ['auth' => $firebaseSecret])->json() ?? [];
        $komoditasRaw  = Http::get($firebaseUrl . 'komoditas.json', ['auth' => $firebaseSecret])->json() ?? [];
        $bangunansRaw  = Http::get($firebaseUrl . 'bangunans.json', ['auth' => $firebaseSecret])->json() ?? [];

        // --- 3. FILTER DATA BANGUNAN (PERBAIKAN DI SINI) ---
        // Kita ubah ke Collection dulu
        $bangunanCollection = collect($bangunansRaw)->map(fn($item) => (object)$item);

        // Filter: Hanya ambil data yang field 'kategori'-nya TIDAK KOSONG
        $validBangunan = $bangunanCollection->filter(function ($item) {
            return !empty($item->kategori); 
        });

        // --- 4. HITUNG STATISTIK UTAMA ---
        $data = [
            'total_penduduk'  => count($penduduksRaw),
            'total_rt_rw'     => count($rtsRaw) + count($rwsRaw),
            'total_komoditas' => count($komoditasRaw),
            'total_bangunan'  => $validBangunan->count(), // Menggunakan hasil filter (seharusnya 4)
        ];

        // --- 5. PROSES CHART GENDER & USIA ---
        $penduduks = collect($penduduksRaw)->map(fn($item) => (object)$item);
        
        $genderCounts = [
            'Laki-laki' => 0,
            'Perempuan' => 0
        ];

        $ageGroups = [
            'Anak-anak (0-10)' => 0,
            'Remaja (11-20)'   => 0,
            'Dewasa (21-50)'   => 0,
            'Lansia (>50)'     => 0,
        ];

        // PENTING: Set Realtime ke Waktu Indonesia Barat (WIB)
        $now = Carbon::now('Asia/Jakarta'); 

        foreach ($penduduks as $p) {
            // A. LOGIKA GENDER
            $genderRaw = $p->jenisKelamin ?? $p->jenis_kelamin ?? '';
            $genderNorm = strtolower($genderRaw);

            if ($genderNorm === 'laki-laki') {
                $genderCounts['Laki-laki']++;
            } elseif ($genderNorm === 'perempuan') {
                $genderCounts['Perempuan']++;
            }

            // B. LOGIKA USIA
            $tglLahir = $p->tanggalLahir ?? $p->tanggal_lahir ?? null;

            if (!empty($tglLahir)) {
                try {
                    $birthDate = Carbon::parse($tglLahir);
                    $age = $birthDate->diffInYears($now);

                    if ($age >= 0 && $age <= 10) {
                        $ageGroups['Anak-anak (0-10)']++;
                    } elseif ($age >= 11 && $age <= 20) {
                        $ageGroups['Remaja (11-20)']++;
                    } elseif ($age >= 21 && $age <= 50) {
                        $ageGroups['Dewasa (21-50)']++;
                    } else {
                        $ageGroups['Lansia (>50)']++;
                    }
                } catch (\Exception $e) {
                    continue; 
                }
            }
        }

        // --- 6. FORMAT DATA UNTUK CHART JS ---
        $genderChartData = [
            'labels' => array_keys($genderCounts),
            'data'   => array_values($genderCounts)
        ];

        $ageChartData = [
            'labels' => array_keys($ageGroups),
            'data'   => array_values($ageGroups)
        ];

        return view('admin.dashboard', compact('data', 'genderChartData', 'ageChartData'));
    }
}