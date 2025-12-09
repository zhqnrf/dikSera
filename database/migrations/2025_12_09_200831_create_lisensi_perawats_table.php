<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lisensi_perawats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perawat_profile_id')
                  ->constrained('perawat_profiles')
                  ->onDelete('cascade');
            $table->string('jenis');
            $table->string('nomor');
            $table->date('tgl_terbit');
            $table->date('tgl_expired');
            $table->string('file_path')->nullable();
            $table->string('status')->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lisensi_perawats');
    }
};
