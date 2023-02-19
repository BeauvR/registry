<?php

namespace Tests\Unit\Models;

use App\Enums\ComposerPackageVersionType;
use App\Models\ComposerPackageVersion;
use PHPUnit\Framework\TestCase;

class ComposerPackageVersionTest extends TestCase
{

    public function test_the_normalized_version_is_correct_for_stable_versions(): void
    {
        $model = new ComposerPackageVersion();
        $model->version_code = '1.0.0';
        $model->version_type = ComposerPackageVersionType::STABLE;

        $this->assertEquals('1.0.0.0', $model->normalized_version);
    }

    public function test_the_normalized_version_is_correct_for_dev_versions(): void
    {
        $model = new ComposerPackageVersion();
        $model->version_code = 'dev-main';
        $model->version_type = ComposerPackageVersionType::DEV;

        $this->assertEquals('dev-main', $model->normalized_version);
    }
}