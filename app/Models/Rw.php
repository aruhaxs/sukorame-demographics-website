<?php

namespace App\Models;

// TAMBAHKAN BARIS INI untuk mengimpor trait HasFactory
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rw extends Model
{
    // Sekarang 'HasFactory' akan dikenali
    use HasFactory;

    protected $table = 'rw';

    protected $fillable = ['nomor_rw', 'ketua_rw'];

    /**
     * Mendefinisikan hubungan one-to-many: satu RW memiliki banyak RT.
     * Nama fungsi diubah menjadi jamak ('rts') sesuai konvensi.
     */
    public function rt()
    {
        return $this->hasMany(Rt::class);
    }
}
