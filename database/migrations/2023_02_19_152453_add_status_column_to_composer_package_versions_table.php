<?php

use App\Enums\ComposerPackageVersionStatus;
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
            $table->string('status')->default(ComposerPackageVersionStatus::DRAFT->value)
                ->after('version_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('composer_package_versions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
