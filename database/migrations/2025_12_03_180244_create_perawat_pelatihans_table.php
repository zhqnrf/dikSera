<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerawatPelatihansTable extends Migration
{
    public function up()
    {
        Schema::create('perawat_pelatihans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('nama_pelatihan', 150)->nullable();
            $table->string('penyelenggara', 150)->nullable();
            $table->string('tempat', 100)->nullable();       // (Dari Controller)
            $table->string('durasi', 50)->nullable();
            $table->date('tanggal_mulai')->nullable();       // (Dari Controller)
            $table->date('tanggal_selesai')->nullable();     // (Dari Controller)
            $table->string('tahun', 4)->nullable();          // Unused di controller saat ini
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('perawat_pelatihans');
    }
}