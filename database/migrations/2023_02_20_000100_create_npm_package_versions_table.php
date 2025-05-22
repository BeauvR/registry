<?php

use App\Models\NpmPackage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('npm_package_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(NpmPackage::class)->constrained();
            $table->string('version_code');
            $table->string('version_type');
            $table->string('status')->default('draft');
            $table->string('source_reference');
            $table->string('storage_path')->nullable();
            $table->string('storage_shasum')->nullable();
            $table->json('package_json_content')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('npm_package_versions');
    }
};
