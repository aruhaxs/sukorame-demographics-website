<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perangkats', function (Blueprint $table) {
            $table->id(); // Membuat kolom 'id' auto-increment
            $table->string('nama'); // Kolom untuk nama perangkat
            $table->string('jabatan'); // Kolom untuk jabatan (misal: Ketua RT 01)
            $table->string('nomor_telepon'); // Kolom untuk nomor telepon
            $table->timestamps(); // Membuat kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perangkats');
    }
};
