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

        // Ambil Data
        $response = Http::get($firebaseUrl . 'penduduks.json', [
            'auth' => $firebaseSecret
        ]);

        $rawData = $response->json() ?? [];

        // Konversi ke Collection Object
        $pendudukData = collect($rawData)->map(function ($item) {
            return (object) $item;
        });

        // 1. Data Dasar
        $totalPenduduk = $pendudukData->count();
        $jumlahLakiLaki = $pendudukData->where('jenisKelamin', 'Laki-laki')->count();
        $jumlahPerempuan = $pendudukData->where('jenisKelamin', 'Perempuan')->count();

        // Hitung KK
        $jumlahKK = $pendudukData->filter(function ($item) {
            return isset($item->statusDiKeluarga) && $item->statusDiKeluarga === 'Kepala Keluarga';
        })->count();

        // 2. Data Pie Chart (Agama, dll)
        $processData = function ($data, $field) {
            $counts = $data->groupBy(function ($item) use ($field) {
                return $item->$field ?? 'Lainnya'; 
            })->map(fn ($item) => $item->count());

            return [
                'labels' => $counts->keys()->toArray(),
                'counts' => $counts->values()->toArray(),
            ];
        };

        $allDemografiData = [
            'agama'      => $processData($pendudukData, 'agama'),
            'gender'     => $processData($pendudukData, 'jenisKelamin'),
            'pendidikan' => $processData($pendudukData, 'pendidikanTerakhir'),
            'pekerjaan'  => $processData($pendudukData, 'pekerjaan'),
        ];

        $agamaDataList = $pendudukData->groupBy(function ($item) {
            return $item->agama ?? 'Lainnya';
        })->map(fn ($item) => $item->count());

        // 3. Data Bar Chart (Usia)
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
        // Kategori Rentang Usia
        $ageRanges = [
            '0-4', '5-9', '10-14', '15-19', '20-24', '25-29', '30-34', '35-39', '40-44', '45-49',
            '50-54', '55-59', '60-64', '65-69', '70-74', '75-79', '80-84', '85+'
        ];

        $dataCounts = array_fill(0, count($ageRanges), 0);
        $now = Carbon::now();

        foreach ($pendudukData as $penduduk) {
            // TARGET LANGSUNG: field 'tanggalLahir'
            if (!isset($penduduk->tanggalLahir) || empty($penduduk->tanggalLahir)) {
                continue; 
            }

            try { 
                // Parse tanggal (misal: "2017-03-22")
                $dateObj = Carbon::parse($penduduk->tanggalLahir);
                $age = $now->diffInYears($dateObj); 
            } catch (\Exception $e) { 
                continue; 
            }

            // Tentukan Index Array berdasarkan umur
            $ageIndex = floor($age / 5);

            // Validasi Index
            if ($ageIndex < 0) continue;
            if ($ageIndex >= count($ageRanges)) $ageIndex = count($ageRanges) - 1;

            $dataCounts[$ageIndex]++;
        }

        return [ 'labels' => $ageRanges, 'total' => $dataCounts ];
    }
}