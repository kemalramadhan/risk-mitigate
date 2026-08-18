<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Judgment AHP-Express milik seorang partisipan pada satu ronde perumusan.
     * Partisipan memilih satu faktor risiko sebagai referensi lalu
     * membandingkannya terhadap (n-1) faktor lain (skala Saaty 6-poin).
     * value = w(reference_factor) / w(other_factor).
     */
    public function up(): void
    {
        Schema::create('formulation_judgments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formulation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reference_factor_id')->constrained('risk_factors')->cascadeOnDelete();
            $table->foreignId('other_factor_id')->constrained('risk_factors')->cascadeOnDelete();
            $table->decimal('value', 12, 6);
            $table->timestamps();

            $table->unique(['formulation_id', 'user_id', 'other_factor_id'], 'fj_unique');
            $table->index(['formulation_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formulation_judgments');
    }
};
