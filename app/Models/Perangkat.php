<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perangkat extends Model
{
    use HasFactory;

    protected $table = 'perangkats'; // Mengatasi konvensi jamak Laravel

    protected $fillable = [
        'nama',
        'jabatan',
        'nomor_telepon',
    ];
}
