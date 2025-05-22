<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NpmPackage>
 */
class NpmPackageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word . '/' . $this->faker->word,
            'git_source' => $this->faker->url(),
        ];
    }
}
