<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Satu ronde perumusan strategi mitigasi (Fitur 3).
     * Dapat dilakukan lebih dari satu kali per proyek.
     */
    public function up(): void
    {
        Schema::create('formulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('open');   // open | completed
            $table->string('method', 20)->nullable();        // single | group
            $table->string('risk_level', 20);                // snapshot level saat ronde dibuat
            $table->timestamp('computed_at')->nullable();
            $table->json('result')->nullable();              // hasil agregasi + ranking + strategi
            $table->timestamps();

            $table->index(['project_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formulations');
    }
};
