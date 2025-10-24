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
            $table->string('nomor_kk', 16)->nullable();
            $table->string('nik', 16)->unique();
            $table->string('nama_lengkap');
            $table->string('nomor_kk', 16)->nullable();
            $table->date('tanggal_lahir');
            $table->foreignId('rw_id')->constrained('rw')->onDelete('cascade');
            $table->foreignId('rt_id')->constrained('rt')->onDelete('cascade');
            $table->string('pekerjaan');
            $table->string('pendidikan_terakhir');
            $table->string('agama');
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
