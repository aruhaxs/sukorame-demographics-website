<?php

namespace App\Http\Controllers;

use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{

    public function index(): View
    {
        $totalPenduduk   = Penduduk::count();
        $jumlahLakiLaki  = Penduduk::where('jenis_kelamin', 'Laki-laki')->count();
        $jumlahPerempuan = Penduduk::where('jenis_kelamin', 'Perempuan')->count();
        
        $totalKK         = Penduduk::distinct('nomor_kk')->count('nomor_kk');

        $ageDataQuery = Penduduk::select(
            DB::raw("CASE
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 0 AND 1 THEN 'Bayi'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 2 AND 12 THEN 'Anak-anak'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 13 AND 17 THEN 'Remaja'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 18 AND 59 THEN 'Dewasa'
                ELSE 'Lansia'
            END as age_group"),
            DB::raw('COUNT(*) as total')
        )
            ->whereNotNull('tanggal_lahir')
            ->groupBy('age_group')
            ->orderByRaw("FIELD(age_group, 'Bayi', 'Anak-anak', 'Remaja', 'Dewasa', 'Lansia')")
            ->get();
        
        $usiaData = [
            'labels' => $ageDataQuery->pluck('age_group'),
            'data'   => $ageDataQuery->pluck('total'),
        ];

        return view('welcome', compact(
            'totalPenduduk',
            'jumlahLakiLaki',
            'jumlahPerempuan',
            'totalKK',
            'usiaData'
        ));
    }
}