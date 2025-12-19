<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bangunan extends Model
{
    use HasFactory;

    protected $table = 'bangunans';

    protected $fillable = [
        'nama_bangunan',
        'kategori',
        'deskripsi',
        'alamat',
        'latitude',
        'longitude',
        'rw_id',
        'rt_id',
        'foto',
    ];

    public function rw(): BelongsTo
    {
        return $this->belongsTo(Rw::class, 'rw_id');
    }

    public function rt(): BelongsTo
    {
        return $this->belongsTo(Rt::class, 'rt_id');
    }
}