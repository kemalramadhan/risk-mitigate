<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Judul paper/dokumen sumber strategi mitigasi (kolom Judul_Sumber pada KB rev2).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('strategy_references', function (Blueprint $table) {
            $table->string('judul_sumber', 500)->nullable()->after('sumber');
        });
    }

    public function down(): void
    {
        Schema::table('strategy_references', function (Blueprint $table) {
            $table->dropColumn('judul_sumber');
        });
    }
};
