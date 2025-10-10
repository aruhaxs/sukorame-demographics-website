<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komoditas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_komoditas',
        'kategori',
        'produksi',
        'periode',
        'produsen',
        'lokasi',
        'harga',
    ];
}
