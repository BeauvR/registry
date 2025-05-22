<?php

namespace Tests\Feature\Npm;

use App\Models\NpmPackage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class NpmPackageIndexTest extends TestCase
{
    use WithFaker;

    public function test_the_endpoint_is_public_accessible(): void
    {
        $this->getJson(route('npm.packages'))
            ->assertOk();
    }

    public function test_the_response_structure_is_good(): void
    {
        $this->getJson(route('npm.packages'))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('packages')
                ->has('metadata-url')
                ->has('available-packages')
            );
    }

    public function test_the_packages_array_is_always_empty(): void
    {
        NpmPackage::factory()->count(5)->create();

        $this->getJson(route('npm.packages'))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('packages', [])
                ->etc()
            );
    }

    public function test_the_metadata_url_is_correct(): void
    {
        $url = $this->faker->url;
        config(['app.url' => $url]);

        $metadataUrl = $url . route('npm.npmPackage.show', ['%package%'], false);

        $this->getJson(route('npm.packages'))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('metadata-url', $metadataUrl)
                ->etc()
            );
    }

    public function test_the_available_packages_array_is_correct(): void
    {
        $packages = NpmPackage::factory()->count(3)->create();

        $this->getJson(route('npm.packages'))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('available-packages', $packages->pluck('name')->toArray())
                ->etc()
            );
    }
}
