<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Assessment = satu putaran perumusan strategi mitigasi yang mencakup
 * KEEMPAT level risiko (Low, Medium, High, Critical) sekaligus. Tidak ada
 * lagi asesmen/triage penentu level di awal.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('open'); // open | completed
            $table->timestamp('computed_at')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
