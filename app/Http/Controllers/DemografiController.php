<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penduduk;
use App\Models\Perangkat;
use Carbon\Carbon;

class DemografiController extends Controller
{
    public function index(Request $request)
    {
        $pendudukData = Penduduk::all();

        // 1. Perhitungan Data Dasar
        $totalPenduduk = $pendudukData->count();
        $jumlahLakiLaki = $pendudukData->where('jenis_kelamin', 'Laki-laki')->count();
        $jumlahPerempuan = $pendudukData->where('jenis_kelamin', 'Perempuan')->count();
        $jumlahKK = 2087;

        // 2. Pemrosesan Data Chart
        $processData = function ($data, $field) {
            $counts = $data->groupBy($field)->map(fn ($item) => $item->count());
            return [
                'labels' => $counts->keys()->toArray(),
                'counts' => $counts->values()->toArray(),
            ];
        };

        $allDemografiData = [
            'agama' => $processData($pendudukData, 'agama'),
            'gender' => $processData($pendudukData, 'jenis_kelamin'),
            'pendidikan' => $processData($pendudukData, 'pendidikan_terakhir'),
            'pekerjaan' => $processData($pendudukData, 'pekerjaan'),
        ];

        $agamaDataList = $pendudukData->groupBy('agama')->map(fn ($item) => $item->count());
        $chartData = $this->processAgeData($pendudukData);


        return view('demografi', [
            'totalPenduduk' => $totalPenduduk,
            'jumlahLakiLaki' => $jumlahLakiLaki,
            'jumlahPerempuan' => $jumlahPerempuan,
            'jumlahKK' => $jumlahKK,
            'agamaData' => $agamaDataList, // Untuk list agama
            'chartData' => $chartData, // Data Bar Chart Usia
            'allDemografiData' => $allDemografiData, // Data Pie Chart Dinamis
        ]);
    }

    private function processAgeData($pendudukData)
    {
        // Logika anti-crash usia yang disempurnakan (tidak berubah)
        $ageRanges = [
            '0-4', '5-9', '10-14', '15-19', '20-24', '25-29', '30-34', '35-39', '40-44', '45-49',
            '50-54', '55-59', '60-64', '65-69', '70-74', '75-79', '80-84', '85+'
        ];

        $dataCounts = array_fill(0, count($ageRanges), 0);
        $now = Carbon::now();

        foreach ($pendudukData as $penduduk) {
            if (empty($penduduk->tanggal_lahir)) continue;
            try { $age = $now->diffInYears($penduduk->tanggal_lahir); } catch (\Exception $e) { continue; }

            $ageIndex = floor($age / 5);

            if ($ageIndex < 0) continue;
            if ($ageIndex >= count($ageRanges)) $ageIndex = count($ageRanges) - 1;

            $dataCounts[$ageIndex]++;
        }

        return [ 'labels' => $ageRanges, 'total' => $dataCounts ];
    }
}
