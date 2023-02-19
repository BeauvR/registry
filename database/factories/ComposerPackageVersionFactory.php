<?php

namespace Database\Factories;

use App\Models\ComposerPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ComposerPackage>
 */
class ComposerPackageVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @throws \Exception
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'composer_package_id' => ComposerPackage::factory(),
            'version_code' => random_int(0, 5) .'.'. random_int(0,5) .'.'. random_int(0,5),
            'version_type' => $this->faker->randomElement(['dev', 'stable']),
            'source_reference' => $this->faker->uuid(),
            'storage_path' => $this->faker->filePath(),
            'storage_shasum' => $this->faker->sha256(),
            'composer_json_content' => [
                'name' => $this->faker->word() . '/' . $this->faker->word(),
            ],
        ];
    }
}
