<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('strategy_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mitigation_strategy_id')->constrained()->cascadeOnDelete();
            $table->string('sumber', 255)->nullable();
            $table->string('lokasi', 500)->nullable();
            $table->text('kutipan')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index('mitigation_strategy_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('strategy_references');
    }
};
