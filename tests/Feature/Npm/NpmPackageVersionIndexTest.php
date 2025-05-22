<?php

namespace Tests\Feature\Npm;

use App\Enums\NpmPackageVersionType;
use App\Models\NpmPackage;
use App\Models\NpmPackageVersion;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class NpmPackageVersionIndexTest extends TestCase
{
    use WithFaker;

    public function test_the_endpoint_is_public_accessible(): void
    {
        $npmPackage = NpmPackage::factory()->create();

        $this->getJson(route('npm.npmPackage.show', [$npmPackage->name]))
            ->assertOk();
    }

    public function test_the_response_structure_is_good(): void
    {
        $version = NpmPackageVersion::factory()
            ->state([
                'version_type' => NpmPackageVersionType::STABLE,
                'package_json_content' => ['extra' => 'extra'],
            ])
            ->create();

        $this->getJson(route('npm.npmPackage.show', [$version->npmPackage->name]))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('packages')
            );
    }

    public function test_the_versions_are_sorted_on_version_code(): void
    {
        $npmPackage = NpmPackage::factory()->create();

        $v1 = NpmPackageVersion::factory()->state([
                'version_type' => NpmPackageVersionType::STABLE,
                'version_code' => '1.0.0',
            ])->for($npmPackage)->create();
        $v2 = NpmPackageVersion::factory()->state([
                'version_type' => NpmPackageVersionType::STABLE,
                'version_code' => '2.0.0',
            ])->for($npmPackage)->create();

        $this->getJson(route('npm.npmPackage.show', [$npmPackage->name]))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('packages', fn (AssertableJson $packages) => $packages
                    ->has($npmPackage->name, fn (AssertableJson $vers) => $vers
                        ->has('0', fn (AssertableJson $ver) => $ver->where('version', $v2->version_code)->etc())
                        ->has('1', fn (AssertableJson $ver) => $ver->where('version', $v1->version_code)->etc())
                    )
                )
            );
    }
}
