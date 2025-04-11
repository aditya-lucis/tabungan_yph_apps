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
        Schema::create('data_anaks', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->unsignedBigInteger('id_karyawan');
            $table->unsignedBigInteger('id_program');
            $table->string('nama_sekolah');
            $table->string('tempat_lahir');
            $table->date('tgl_lahir');
            $table->string('fc_ktp')->nullable();
            $table->string('surat_sekolah')->nullable();
            $table->string('fc_raport')->nullable();
            $table->string('fc_rek_sekolah')->nullable();
            $table->timestamps();

            $table->foreign('id_karyawan')->on('employees')->references('id');
            $table->foreign('id_program')->on('programs')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_anaks');
    }
};
