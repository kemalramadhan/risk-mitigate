<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Perluas unique constraint agar tabel formulation_judgments dapat menyimpan
 * dua bentuk judgment:
 *   - AHP-Express (multi partisipan): reference_factor_id konstan, other bervariasi.
 *   - AHP standar (1 partisipan): SETIAP pasangan faktor (i, j) disimpan sebagai
 *     reference_factor_id = i, other_factor_id = j. other_factor_id kini bisa
 *     berulang, sehingga reference_factor_id perlu ikut dalam unique key.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formulation_judgments', function (Blueprint $table) {
            $table->dropUnique('fj_unique');
            $table->unique(
                ['formulation_id', 'user_id', 'reference_factor_id', 'other_factor_id'],
                'fj_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('formulation_judgments', function (Blueprint $table) {
            $table->dropUnique('fj_unique');
            $table->unique(['formulation_id', 'user_id', 'other_factor_id'], 'fj_unique');
        });
    }
};
