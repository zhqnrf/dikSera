<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerawatTandajasaTable extends Migration
{
    public function up()
    {
        Schema::create('perawat_tandajasa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            $table->string('nama_penghargaan', 150)->nullable();
            $table->string('instansi_pemberi', 150)->nullable(); // <-- (Controller pakai ini)
            $table->string('tahun', 10)->nullable();             // <-- UPDATE (Controller max:10)
            $table->string('nomor_sk', 100)->nullable();         // <-- BARU
            $table->date('tanggal_sk')->nullable();              // <-- BARU
            $table->string('keterangan', 255)->nullable();       // Unused

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('perawat_tandajasa');
    }
}