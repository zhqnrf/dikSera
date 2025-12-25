<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerawatLisensisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perawat_lisensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama');
            $table->string('lembaga');
            $table->string('nomor');
            $table->date('tgl_terbit');
            $table->date('tgl_expired');
            $table->string('file_path')->nullable();
            $table->enum('metode_perpanjangan', ['pg_only', 'pg_interview', 'interview_only'])->default('pg_only');
            $table->string('unit_kerja_saat_buat')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('perawat_lisensis');
    }
}
