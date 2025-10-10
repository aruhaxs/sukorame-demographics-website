<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penduduk extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lengkap',
        'nomor_kk',
        'nik',
        'tanggal_lahir',
        'jenis_kelamin',
        'rt',
        'rw',
        'pekerjaan',
        'pendidikan_terakhir',
        'agama',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date', // Penting untuk perhitungan usia
    ];
}
