<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();      // unique identifier, mis. RM-IT-9F3A2B
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('domain', 10);              // IT | RnD
            $table->text('objective')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('triage_answers')->nullable();  // {char: {label, score}}
            $table->unsignedSmallInteger('triage_total')->nullable();
            $table->string('risk_level', 20)->nullable();
            $table->boolean('mitigation_enabled')->default(false);
            $table->timestamps();

            $table->index('owner_id');
            $table->index(['domain', 'risk_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
