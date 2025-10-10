<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komoditas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_komoditas'); // Nama Komoditas (Padi, Jagung)
            $table->string('kategori')->nullable(); // Kategori (Pertanian, Peternakan)
            $table->string('produksi')->nullable(); // Jumlah produksi (misal: 250 ton)
            $table->string('lokasi')->nullable(); // Lokasi/Daerah (misal: RW 01)
            $table->string('harga')->nullable(); // Harga jual
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komoditas');
    }
};
