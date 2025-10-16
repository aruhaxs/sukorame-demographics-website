<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bangunan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     * Laravel akan otomatis mencari 'bangunans' jika ini tidak didefinisikan.
     */
    protected $table = 'bangunans';

    /**
     * Kolom-kolom yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'nama_bangunan',
        'kategori',
        'deskripsi',
        'foto',
        'latitude',
        'longitude',
    ];
}
