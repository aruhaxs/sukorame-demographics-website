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
        Schema::create('penduduks', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->unique();
            $table->string('nama_lengkap');
            $table->string('nomor_kk', 16)->nullable(); // <-- TAMBAHAN
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']); // <-- LEBIH BAIK

            // --- INI BAGIAN YANG DIPERBAIKI ---
            // Mengganti string('rt') dan string('rw')

            // Ini akan membuat kolom 'rw_id' (BIGINT) dan menghubungkannya ke 'id' di tabel 'rw'
            $table->foreignId('rw_id')->constrained('rw');

            // Ini akan membuat kolom 'rt_id' (BIGINT) dan menghubungkannya ke 'id' di tabel 'rt'
            $table->foreignId('rt_id')->constrained('rt');
            // --- SELESAI PERBAIKAN ---

            $table->string('pekerjaan')->nullable(); // Sebaiknya nullable
            $table->string('pendidikan_terakhir')->nullable(); // Sebaiknya nullable
            $table->string('agama', 50)->nullable(); // Sebaiknya nullable

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penduduks');
    }
};
