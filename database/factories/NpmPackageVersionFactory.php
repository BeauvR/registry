<?php

namespace Database\Factories;

use App\Enums\NpmPackageVersionStatus;
use App\Models\NpmPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NpmPackageVersion>
 */
class NpmPackageVersionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'npm_package_id' => NpmPackage::factory(),
            'version_code' => random_int(0, 5) . '.' . random_int(0, 5) . '.' . random_int(0, 5),
            'version_type' => $this->faker->randomElement(['dev', 'stable']),
            'source_reference' => $this->faker->uuid(),
            'storage_path' => $this->faker->filePath(),
            'storage_shasum' => $this->faker->sha256(),
            'package_json_content' => [
                'name' => $this->faker->word() . '/' . $this->faker->word(),
            ],
            'status' => NpmPackageVersionStatus::READY,
        ];
    }
}
