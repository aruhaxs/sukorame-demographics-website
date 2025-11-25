<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index(): View
    {
        // 1. AMBIL DATA
        $firebaseUrl = env('FIREBASE_DATABASE_URL');
        $firebaseSecret = env('FIREBASE_DB_SECRET');

        if (!str_ends_with($firebaseUrl, '/')) $firebaseUrl .= '/';

        $response = Http::get($firebaseUrl . 'penduduks.json', ['auth' => $firebaseSecret]);
        $rawData = $response->json() ?? [];

        $pendudukData = collect($rawData)->map(fn($item) => (object) $item);

        // 2. HITUNG DATA DASAR
        $totalPenduduk = $pendudukData->count();
        $jumlahLakiLaki = $pendudukData->where('jenisKelamin', 'Laki-laki')->count();
        $jumlahPerempuan = $pendudukData->where('jenisKelamin', 'Perempuan')->count();
        
        // Hitung KK berdasarkan string "Kepala Keluarga"
        $totalKK = $pendudukData->filter(function ($item) {
            return isset($item->statusDiKeluarga) && $item->statusDiKeluarga === 'Kepala Keluarga';
        })->count();

        // 3. HITUNG USIA (Logic Chart)
        // Kategori sesuai gambar referensi: 0-4, 5-9, dst.
        $ageRanges = [
            '0-4', '5-9', '10-14', '15-19', '20-24', '25-29', '30-34', '35-39', '40-44', '45-49',
            '50-54', '55-59', '60-64', '65-69', '70-74', '75-79', '80-84', '85+'
        ];
        
        $dataCounts = array_fill(0, count($ageRanges), 0);
        $now = Carbon::now();

        foreach ($pendudukData as $penduduk) {
            // Cek kedua kemungkinan format penulisan di Firebase
            $tglLahir = $penduduk->tanggalLahir ?? $penduduk->tanggal_lahir ?? null;

            if (empty($tglLahir)) continue;

            try {
                $age = $now->diffInYears(Carbon::parse($tglLahir));
                
                // Rumus matematika sederhana untuk menentukan index array
                // Contoh: Umur 7 tahun. 7 / 5 = 1.4 -> floor jadi 1. Index 1 adalah '5-9'
                $ageIndex = floor($age / 5);

                if ($ageIndex < 0) continue;
                if ($ageIndex >= count($ageRanges)) $ageIndex = count($ageRanges) - 1; // Untuk 85+

                $dataCounts[$ageIndex]++;
            } catch (\Exception $e) { continue; }
        }

        $usiaData = [
            'labels' => $ageRanges,
            'data'   => $dataCounts
        ];

        return view('welcome', compact(
            'totalPenduduk', 'jumlahLakiLaki', 'jumlahPerempuan', 'totalKK', 'usiaData'
        ));
    }
}