<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Setiap assessment memiliki 4 formulation (satu per level risiko).
 * formulation.risk_level tetap menandai level-nya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formulations', function (Blueprint $table) {
            $table->foreignId('assessment_id')->nullable()->after('project_id')
                  ->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('formulations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assessment_id');
        });
    }
};
