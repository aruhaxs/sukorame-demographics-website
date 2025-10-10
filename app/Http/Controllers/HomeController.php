<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penduduk;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $pendudukData = Penduduk::all();

        $totalPenduduk = $pendudukData->count();
        $jumlahLakiLaki = $pendudukData->where('jenis_kelamin', 'Laki-laki')->count();
        $jumlahPerempuan = $pendudukData->where('jenis_kelamin', 'Perempuan')->count();

        // Mengambil data kategori usia yang sudah dihitung
        $usiaData = $this->getUsiaKategoriData($pendudukData);

        // Kirim data yang dibutuhkan ke view
        return view('welcome', compact(
            'totalPenduduk',
            'jumlahLakiLaki',
            'jumlahPerempuan',
            'usiaData' // <-- Mengirim data usia yang sudah diproses
        ));
    }

    /**
     * Mengelompokkan penduduk berdasarkan kategori usia dan menghitung persentase
     * setiap kategori dari total penduduk yang valid.
     */
    private function getUsiaKategoriData($pendudukData)
    {
        $kategori = [
            'Bayi' => 0,       // 0 - 1
            'Anak-anak' => 0,  // 2 - 12
            'Remaja' => 0,     // 13 - 17
            'Dewasa' => 0,     // 18 - 59
            'Lansia' => 0,     // 60+
        ];

        $now = Carbon::now();
        $validData = 0; // Total data yang memiliki tanggal lahir valid

        foreach ($pendudukData as $penduduk) {
            if (empty($penduduk->tanggal_lahir)) continue;

            try {
                $age = $now->diffInYears(Carbon::parse($penduduk->tanggal_lahir));
            } catch (\Exception $e) {
                continue;
            }

            $validData++; // Hitung sebagai data valid

            // Klasifikasi usia
            if ($age >= 0 && $age <= 1) {
                $kategori['Bayi']++;
            } elseif ($age >= 2 && $age <= 12) {
                $kategori['Anak-anak']++;
            } elseif ($age >= 13 && $age <= 17) {
                $kategori['Remaja']++;
            } elseif ($age >= 18 && $age <= 59) {
                $kategori['Dewasa']++;
            } elseif ($age >= 60) {
                $kategori['Lansia']++;
            }
        }

        // Menghitung persentase setiap kategori dari total penduduk valid (untuk visualisasi bar chart)
        $persentase = [];
        if ($validData > 0) {
            foreach ($kategori as $label => $count) {
                // Persentase kategori dari total penduduk valid
                $persentase[$label] = ($count / $validData) * 100;
            }
        }

        return [
            'counts' => $kategori, // Jumlah hitungan (untuk label)
            'percentages' => $persentase, // Persentase (untuk width bar)
            'total' => $validData
        ];
    }
}
