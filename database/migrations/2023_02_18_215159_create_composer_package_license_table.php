<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('composer_package_license', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(\App\Models\ComposerPackage::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\License::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('composer_package_license');
    }
};
