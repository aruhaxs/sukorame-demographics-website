<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penduduks', function (Blueprint $table) {
            $table->string('nik', 16)->primary();
            $table->string('nomor_kk', 16)->nullable();
            $table->string('nama_lengkap');
            $table->string('jenis_kelamin');
            $table->date('tanggal_lahir');
            $table->foreignId('rw_id')->constrained('rw')->onDelete('cascade');
            $table->foreignId('rt_id')->constrained('rt')->onDelete('cascade');
            $table->string('pekerjaan')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('agama')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penduduks');
    }
};