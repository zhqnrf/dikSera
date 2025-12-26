<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('perawat_data_tambahans', function (Blueprint $table) {
            $table->boolean('is_lifetime')->default(false)->after('tgl_expired');
            $table->boolean('lifetime_approved')->default(false)->after('is_lifetime');
            $table->unsignedBigInteger('lifetime_approved_by')->nullable()->after('lifetime_approved');
            $table->timestamp('lifetime_approved_at')->nullable()->after('lifetime_approved_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('perawat_data_tambahans', function (Blueprint $table) {
            $table->dropColumn(['is_lifetime', 'lifetime_approved', 'lifetime_approved_by', 'lifetime_approved_at']);
        });
    }
};
