<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Penilai (assessor) yang dipilih manajer untuk sebuah assessment.
 * Bisa mencakup manajer sendiri dan/atau anggota yang sudah ditambahkan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['assessment_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_participants');
    }
};
