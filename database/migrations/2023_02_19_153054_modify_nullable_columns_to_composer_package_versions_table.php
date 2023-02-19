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
        Schema::table('composer_package_versions', function (Blueprint $table) {
            $table->string('storage_path')->nullable()->change();
            $table->string('storage_shasum')->nullable()->change();
            $table->json('composer_json_content')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('composer_package_versions', function (Blueprint $table) {
            $table->string('storage_path')->nullable(false)->change();
            $table->string('storage_shasum')->nullable(false)->change();
            $table->json('composer_json_content')->nullable(false)->change();
        });
    }
};
