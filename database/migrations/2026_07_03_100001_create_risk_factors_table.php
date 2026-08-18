<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_factors', function (Blueprint $table) {
            $table->id();
            $table->string('domain', 10);            // IT | RnD
            $table->string('risk_level', 20);        // Low | Medium | High | Critical
            $table->string('name', 255);             // raw Faktor_Risiko
            $table->string('display_name', 255);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['domain', 'risk_level', 'name']);
            $table->index(['domain', 'risk_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_factors');
    }
};
