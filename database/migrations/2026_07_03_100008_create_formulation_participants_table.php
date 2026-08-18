<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formulation_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formulation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reference_factor_id')->nullable()
                  ->constrained('risk_factors')->nullOnDelete();
            $table->decimal('weight', 12, 8)->nullable();  // snapshot bobot expert saat kalkulasi
            $table->timestamp('submitted_at')->nullable();
            $table->json('priority_vector')->nullable();    // {risk_factor_id: weight, ...}
            $table->timestamps();

            $table->unique(['formulation_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formulation_participants');
    }
};
