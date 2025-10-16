<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rt extends Model
{
    use HasFactory;

    protected $table = 'rt';

    /**
     * Kolom-kolom yang dapat diisi secara massal.
     */
    protected $fillable = [
        'rw_id',
        'nomor_rt',
        'ketua_rt',
    ];

    /**
     * Mendefinisikan hubungan "satu RT dimiliki oleh satu RW".
     * Nama fungsi disarankan dalam bentuk tunggal (rw).
     */
    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }
}
