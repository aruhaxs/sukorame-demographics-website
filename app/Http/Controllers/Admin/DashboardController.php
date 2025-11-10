<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Komoditas;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Bangunan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama.
     */
    public function index(): View
    {
        $data = [
            'total_penduduk' => Penduduk::count(),
            'total_rt_rw'     => Rt::count() + Rw::count(),
            'total_komoditas' => Komoditas::count(),
            'total_bangunan' => Bangunan::count(),
        ];

        // Data Chart Gender
        $genderData = Penduduk::select('jenis_kelamin as gender', DB::raw('COUNT(*) as total'))
            ->groupBy('gender')
            ->get();
        $genderChartData = [
            'labels' => $genderData->pluck('gender')->map(fn($g) => $g ?? 'Tidak Diketahui'),
            'data' => $genderData->pluck('total')
        ];

        // Data Chart Usia
        $ageData = Penduduk::select(
            DB::raw("CASE
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 0 AND 10 THEN 'Anak-anak (0-10)'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 11 AND 20 THEN 'Remaja (11-20)'
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 21 AND 50 THEN 'Dewasa (21-50)'
                ELSE 'Lansia (>50)'
            END as age_group"),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('age_group')
            ->orderByRaw("FIELD(age_group, 'Anak-anak (0-10)', 'Remaja (11-20)', 'Dewasa (21-50)', 'Lansia (>50)')")
            ->get();

        $ageChartData = ['labels' => $ageData->pluck('age_group'), 'data' => $ageData->pluck('total')];

        return view('admin.dashboard', compact('data', 'genderChartData', 'ageChartData'));
    }
}
