<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Deskripsi faktor risiko.
 *
 * base_name  = nama dasar faktor tanpa kategori, mis. "Team_Experience_Level"
 *              dari "Team_Experience_Level_Expert".
 * description= penjelasan faktor (33 faktor dasar, lihat
 *              database/data/factor_descriptions.php).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('risk_factors', function (Blueprint $table) {
            $table->string('base_name', 255)->nullable()->after('name');
            $table->text('description')->nullable()->after('display_name');
        });
    }

    public function down(): void
    {
        Schema::table('risk_factors', function (Blueprint $table) {
            $table->dropColumn(['base_name', 'description']);
        });
    }
};
