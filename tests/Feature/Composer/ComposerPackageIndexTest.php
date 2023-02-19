<?php

namespace Tests\Feature\Composer;

use App\Models\ComposerPackage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ComposerPackageIndexTest extends TestCase
{
    use WithFaker;

    public function test_the_endpoint_is_public_accessible(): void
    {
        $this->getJson(route('composer.packages'))
            ->assertOk();
    }

    public function test_the_response_structure_is_good(): void
    {
        $this->getJson(route('composer.packages'))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('packages')
                ->has('metadata-url')
                ->has('available-packages')
            );
    }

    /**
     * @throws \Exception
     */
    public function test_the_packages_array_is_always_empty(): void
    {
        ComposerPackage::factory()
            ->count(random_int(10, 99))
            ->create();

        $this->getJson(route('composer.packages'))
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

        $metadataUrl = $url . route('composer.composerPackage.show', ['%package%'], false);

        $this->getJson(route('composer.packages'))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('metadata-url', $metadataUrl)
                ->etc()
            );
    }

    /**
     * @throws \Exception
     */
    public function test_the_available_packages_array_is_correct(): void
    {
        $packages = ComposerPackage::factory()
            ->count(random_int(10, 99))
            ->create();

        $this->getJson(route('composer.packages'))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('available-packages', $packages->pluck('name')->toArray())
                ->etc()
            );
    }
}
