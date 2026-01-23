<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengajuanSertifikatsTable extends Migration
{
    public function up()
    {
        Schema::create('pengajuan_sertifikats', function (Blueprint $table) {
            $table->id();

            // Relasi ke User
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Relasi ke Lisensi Lama (Nullable karena Pengajuan Baru tidak punya lisensi lama)
            $table->foreignId('lisensi_lama_id')
                ->nullable()
                ->constrained('perawat_lisensis')
                ->onDelete('cascade');

            // Status & Jenis Pengajuan
            $table->string('status')->default('pending');
            $table->enum('jenis_pengajuan', ['baru', 'lama'])->default('lama'); // Kolom Baru

            // Metode (Nullable untuk Pengajuan Baru, nanti di-set Admin)
            $table->enum('metode', ['pg_only', 'pg_interview', 'interview_only'])->nullable();

            // === KOLOM DOKUMEN (FULL REVISI) ===
            $table->string('file_rekomendasi')->nullable();      // Upload jika Lama
            $table->string('file_sertifikat_lama')->nullable();   // Upload jika Lama
            $table->string('file_dokumen_baru')->nullable();      // Upload jika Baru (PDF)
            $table->text('link_gdrive')->nullable();              // Wajib untuk keduanya

            // Kolom Jadwal & Tanggung Jawab (Bawaan)
            $table->foreignId('penanggung_jawab_id')->nullable()->constrained('penanggung_jawab_ujians')->onDelete('set null');
            $table->date('tgl_wawancara')->nullable();
            $table->string('lokasi_wawancara')->nullable();

            // Catatan / Keterangan Tambahan
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengajuan_sertifikats');
    }
}
