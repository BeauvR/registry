<?php

use App\Models\ComposerPackage;
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
        Schema::create('composer_package_versions', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(ComposerPackage::class)
                ->constrained();

            $table->string('version_code');
            $table->string('version_type');
            $table->string('source_reference');

            $table->string('storage_path');
            $table->string('storage_shasum');

            $table->json('composer_json_content');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('composer_package_versions');
    }
};
