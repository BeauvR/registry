<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('npm_package_license', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\NpmPackage::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\License::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('npm_package_license');
    }
};
