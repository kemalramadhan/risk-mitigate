<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mitigation_strategies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_factor_id')->constrained()->cascadeOnDelete();
            $table->text('strategy_text');
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index('risk_factor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mitigation_strategies');
    }
};
