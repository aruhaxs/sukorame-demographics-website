<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    /**
     * Menampilkan Halaman Beranda / Dashboard
     */
    public function index(): View
    {
        // 1. AMBIL DATA DARI FIREBASE
        $data = $this->fetchFirebaseData();

        // 2. OLAH DATA PENDUDUK (Termasuk Gender & KK)
        $statsPenduduk = $this->processPendudukStats($data['penduduk']);

        // 3. OLAH DATA BANGUNAN (Hanya Total)
        $totalBangunan = $this->processBangunanStats($data['bangunan']);

        // 4. KIRIM KE VIEW
        return view('welcome', [
            'totalPenduduk'  => $statsPenduduk['total'],
            'totalKK'        => $statsPenduduk['kk'],
            'totalLaki'      => $statsPenduduk['laki'],
            'totalPerempuan' => $statsPenduduk['perempuan'],
            'totalBangunan'  => $totalBangunan,
        ]);
    }

    /**
     * ------------------------------------------------------------------
     * PRIVATE METHODS (HELPER)
     * ------------------------------------------------------------------
     */

    /**
     * Mengambil data mentah dari Firebase secara paralel
     */
    private function fetchFirebaseData(): array
    {
        $firebaseUrl = env('FIREBASE_DATABASE_URL');
        $firebaseSecret = env('FIREBASE_DB_SECRET');

        // Pastikan URL berakhiran slash
        if (!str_ends_with($firebaseUrl, '/')) {
            $firebaseUrl .= '/';
        }

        // Request Paralel agar loading lebih cepat
        $responses = Http::pool(fn ($pool) => [
            $pool->as('penduduk')->get($firebaseUrl . 'penduduks.json', ['auth' => $firebaseSecret]),
            $pool->as('bangunan')->get($firebaseUrl . 'bangunans.json', ['auth' => $firebaseSecret]),
        ]);

        return [
            'penduduk' => $responses['penduduk']->json() ?? [],
            'bangunan' => $responses['bangunan']->json() ?? [],
        ];
    }

    /**
     * Menghitung statistik kependudukan (Total, KK, Laki, Perempuan)
     */
    private function processPendudukStats(array $rawData): array
    {
        $data = collect($rawData)->map(fn($item) => (object) $item);

        return [
            'total' => $data->count(),
            
            // Hitung Kepala Keluarga
            'kk' => $data->filter(function ($item) {
                return isset($item->statusDiKeluarga) && $item->statusDiKeluarga === 'Kepala Keluarga';
            })->count(),

            // Hitung Laki-laki (Field: 'jenisKelamin')
            'laki' => $data->filter(function ($item) {
                return isset($item->jenisKelamin) && $item->jenisKelamin === 'Laki-laki';
            })->count(),

            // Hitung Perempuan (Field: 'jenisKelamin')
            'perempuan' => $data->filter(function ($item) {
                return isset($item->jenisKelamin) && $item->jenisKelamin === 'Perempuan';
            })->count(),
        ];
    }

    /**
     * Menghitung statistik bangunan (Hanya bangunan valid)
     */
    private function processBangunanStats(array $rawData): int
    {
        $data = collect($rawData)->map(fn($item) => (object) $item);

        // Filter: Hanya ambil yang field 'kategori'-nya ada isinya
        $validBangunan = $data->filter(function ($item) {
            return !empty($item->kategori);
        });

        return $validBangunan->count();
    }
}