<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- 1. PASTIKAN INI DITAMBAHKAN

class Penduduk extends Model
{
    use HasFactory;

    /**
     * Attribut yang bisa diisi secara massal.
     */
    protected $fillable = [
        'nama_lengkap',
        'nomor_kk',
        'nik',
        'tanggal_lahir',
        'jenis_kelamin',
        'rt_id', // <-- 2. DIUBAH DARI 'rt'
        'rw_id', // <-- 3. DIUBAH DARI 'rw'
        'pekerjaan',
        'pendidikan_terakhir',
        'agama',
    ];

    /**
     * Tipe data bawaan.
     */
    protected $casts = [
        'tanggal_lahir' => 'date', // Penting untuk perhitungan usia
    ];

    /**
     * ========================================================
     * 4. TAMBAHKAN DUA FUNGSI RELASI INI
     * ========================================================
     */

    /**
     * Mendapatkan data RW yang terkait dengan penduduk.
     * Nama fungsi 'rw' ini harus cocok dengan panggilan with(['rw', ...])
     */
    public function rw(): BelongsTo
    {
        // Model ini 'belongsTo' (milik) Model Rw
        // Laravel akan mencari 'rw_id' di tabel penduduk
        return $this->belongsTo(Rw::class, 'rw_id');
    }

    /**
     * Mendapatkan data RT yang terkait dengan penduduk.
     * Nama fungsi 'rt' ini harus cocok dengan panggilan with(['rt'])
     */
    public function rt(): BelongsTo
    {
        // Model ini 'belongsTo' (milik) Model Rt
        // Laravel akan mencari 'rt_id' di tabel penduduk
        return $this->belongsTo(Rt::class, 'rt_id');
    }
}
