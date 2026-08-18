<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Judgment AHP-Express milik owner untuk pembobotan expert (Fitur 2).
     * Owner memilih satu expert sebagai referensi lalu membandingkannya
     * terhadap (n-1) expert lainnya memakai skala Saaty 6-poin.
     * value = w(reference) / w(other).
     */
    public function up(): void
    {
        Schema::create('expert_weight_judgments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reference_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('other_user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('value', 12, 6);   // rasio w_ref/w_other (1..6 atau 1/6..1)
            $table->timestamps();

            $table->unique(['project_id', 'reference_user_id', 'other_user_id'], 'ewj_unique');
            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expert_weight_judgments');
    }
};
