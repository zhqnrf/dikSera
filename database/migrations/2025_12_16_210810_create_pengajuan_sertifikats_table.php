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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // [PENTING] Gunakan 'cascade' agar jika Lisensi dihapus, pengajuan ini ikut hilang (Reset)
            $table->foreignId('lisensi_lama_id')
                ->nullable()
                ->constrained('perawat_lisensis')
                ->onDelete('cascade');

            $table->string('status')->default('pending');

            // Nullable agar bisa menampung 'Lisensi Baru' (metode = null)
            $table->enum('metode', ['pg_only', 'pg_interview', 'interview_only'])->nullable();

            // Kolom bawaan Anda (tetap dipertahankan)
            $table->foreignId('penanggung_jawab_id')->nullable()->constrained('penanggung_jawab_ujians')->onDelete('set null');
            $table->date('tgl_wawancara')->nullable();
            $table->string('lokasi_wawancara')->nullable();

            // [PENTING] Tambahkan ini untuk menyimpan catatan status
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengajuan_sertifikats');
    }
}
