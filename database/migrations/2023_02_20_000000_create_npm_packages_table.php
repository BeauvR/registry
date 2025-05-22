<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('npm_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('git_source');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('npm_packages');
    }
};
