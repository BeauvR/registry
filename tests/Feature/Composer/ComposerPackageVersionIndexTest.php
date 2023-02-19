<?php

namespace Tests\Feature\Composer;

use App\Enums\ComposerPackageVersionType;
use App\Models\ComposerPackage;
use App\Models\ComposerPackageVersion;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ComposerPackageVersionIndexTest extends TestCase
{
    use WithFaker;

    public function test_the_endpoint_is_public_accessible(): void
    {
        $composerPackage = ComposerPackage::factory()
            ->create();

        $this->getJson(route('composer.composerPackage.show', [$composerPackage->name]))
            ->assertOk();
    }

    public function test_the_response_structure_is_good(): void
    {
        $composerPackageVersion = ComposerPackageVersion::factory()
            ->state([
                'version_type' => ComposerPackageVersionType::STABLE,
                'composer_json_content' => [
                    'extra' => 'extra',
                ],
            ])
            ->create();

        $this->getJson(route('composer.composerPackage.show', [$composerPackageVersion->composerPackage->name]))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('packages', fn (AssertableJson $packages) => $packages
                    ->has(
                        $composerPackageVersion->composerPackage->name,
                        fn (AssertableJson $packageVersions) => $packageVersions
                            ->has('0', fn (AssertableJson $packageVersion) => $packageVersion
                                ->has('name')
                                ->has('version')
                                ->has('version_normalized')
                                ->has('source', fn (AssertableJson $source) => $source
                                    ->has('type')
                                    ->has('url')
                                    ->has('reference'),
                                )
                                ->has('dist', fn (AssertableJson $dist) => $dist
                                    ->has('type')
                                    ->has('url')
                                    ->has('reference')
                                    ->has('shasum'),
                                )
                                ->has('type')
                                ->has('time')
                                ->has('extra'),
                            ),
                    ),
                ));
    }

    public function test_the_versions_are_sorted_on_version_code(): void
    {
        $composerPackage = ComposerPackage::factory()
            ->create();

        $composerPackageVersion1 = ComposerPackageVersion::factory()
            ->state([
                'version_type' => ComposerPackageVersionType::STABLE,
                'version_code' => '1.0.0',
            ])
            ->for($composerPackage)
            ->create();

        $composerPackageVersion2 = ComposerPackageVersion::factory()
            ->state([
                'version_type' => ComposerPackageVersionType::STABLE,
                'version_code' => '2.0.0',
            ])
            ->for($composerPackage)
            ->create();

        $this->getJson(route('composer.composerPackage.show', [$composerPackage->name]))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('packages', fn (AssertableJson $packages) => $packages
                    ->has($composerPackage->name, fn (AssertableJson $packageVersions) => $packageVersions
                        ->has('0', fn (AssertableJson $packageVersion) => $packageVersion
                            ->where('version', $composerPackageVersion2->version_code)
                            ->etc(),
                        )
                        ->has('1', fn (AssertableJson $packageVersion) => $packageVersion
                            ->where('version', $composerPackageVersion1->version_code)
                            ->etc(),
                        ),
                    ),
                ));
    }

    public function test_dev_versions_are_only_returned_when_requested(): void
    {
        $composerPackage = ComposerPackage::factory()
            ->create();

        ComposerPackageVersion::factory()
            ->state([
                'version_type' => ComposerPackageVersionType::STABLE,
                'version_code' => '1.0.0',
            ])
            ->for($composerPackage)
            ->create();

        $composerPackageVersion2 = ComposerPackageVersion::factory()
            ->state([
                'version_type' => ComposerPackageVersionType::DEV,
                'version_code' => 'dev-main',
            ])
            ->for($composerPackage)
            ->create();

        $this->getJson(route('composer.composerPackage.show', [$composerPackage->name, '~dev']))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('packages', fn (AssertableJson $packages) => $packages
                    ->has($composerPackage->name, fn (AssertableJson $packageVersions) => $packageVersions
                        ->has('0', fn (AssertableJson $packageVersion) => $packageVersion
                            ->where('version', $composerPackageVersion2->version_code)
                            ->etc(),
                        ),
                    ),
                ));
    }

    public function test_stable_versions_are_only_returned_when_requested(): void
    {
        $composerPackage = ComposerPackage::factory()
            ->create();

        $composerPackageVersion1 = ComposerPackageVersion::factory()
            ->state([
                'version_type' => ComposerPackageVersionType::STABLE,
                'version_code' => '1.0.0',
            ])
            ->for($composerPackage)
            ->create();

        ComposerPackageVersion::factory()
            ->state([
                'version_type' => ComposerPackageVersionType::DEV,
                'version_code' => 'dev-main',
            ])
            ->for($composerPackage)
            ->create();

        $this->getJson(route('composer.composerPackage.show', [$composerPackage->name]))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('packages', fn (AssertableJson $packages) => $packages
                    ->has($composerPackage->name, fn (AssertableJson $packageVersions) => $packageVersions
                        ->has('0', fn (AssertableJson $packageVersion) => $packageVersion
                            ->where('version', $composerPackageVersion1->version_code)
                            ->etc(),
                        ),
                    ),
                ));
    }

    public function test_the_correct_root_properties_are_returned(): void
    {
        $composerPackageVersion = ComposerPackageVersion::factory()
            ->state([
                'version_type' => ComposerPackageVersionType::STABLE,
                'composer_json_content' => [
                    'extra' => 'extra',
                ],
            ])
            ->create();

        $this->getJson(route('composer.composerPackage.show', [$composerPackageVersion->composerPackage->name]))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('packages', fn (AssertableJson $packages) => $packages
                    ->has(
                        $composerPackageVersion->composerPackage->name,
                        fn (AssertableJson $packageVersions) => $packageVersions
                            ->has('0', fn (AssertableJson $packageVersion) => $packageVersion
                                ->where('name', $composerPackageVersion->composerPackage->name)
                                ->where('version', $composerPackageVersion->version_code)
                                ->where('version_normalized', $composerPackageVersion->version_code)
                                ->where('type', 'library')
                                ->where('time', $composerPackageVersion->created_at->toIso8601String())
                                ->where('extra', $composerPackageVersion->composer_json_content['extra'])
                                ->etc(),
                            ),
                    ),
                ));
    }

    public function test_the_correct_source_properties_are_returned(): void
    {
        $composerPackageVersion = ComposerPackageVersion::factory()
            ->state([
                'version_type' => ComposerPackageVersionType::STABLE,
            ])
            ->create();

        $this->getJson(route('composer.composerPackage.show', [$composerPackageVersion->composerPackage->name]))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('packages', fn (AssertableJson $packages) => $packages
                    ->has(
                        $composerPackageVersion->composerPackage->name,
                        fn (AssertableJson $packageVersions) => $packageVersions
                            ->has('0', fn (AssertableJson $packageVersion) => $packageVersion
                                ->has('source', fn (AssertableJson $source) => $source
                                    ->where('type', 'git')
                                    ->where('url', $composerPackageVersion->composerPackage->git_source)
                                    ->where('reference', $composerPackageVersion->source_reference),
                                )
                                ->etc(),
                            ),
                    ),
                ));
    }

    public function test_the_correct_dist_properties_are_returned(): void
    {
        $composerPackageVersion = ComposerPackageVersion::factory()
            ->state([
                'version_type' => ComposerPackageVersionType::STABLE,
            ])
            ->create();

        $distUrl = config('app.url') . route(
                'composer.composerPackage.composerPackageVersion.download',
                [$composerPackageVersion->composerPackage, $composerPackageVersion],
                false,
            );

        $this->getJson(route('composer.composerPackage.show', [$composerPackageVersion->composerPackage->name]))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('packages', fn (AssertableJson $packages) => $packages
                    ->has(
                        $composerPackageVersion->composerPackage->name,
                        fn (AssertableJson $packageVersions) => $packageVersions
                            ->has('0', fn (AssertableJson $packageVersion) => $packageVersion
                                ->has('dist', fn (AssertableJson $dist) => $dist
                                    ->where('type', 'zip')
                                    ->where('url', $distUrl)
                                    ->where('reference', $composerPackageVersion->source_reference)
                                    ->where('shasum', $composerPackageVersion->storage_shasum),
                                )
                                ->etc(),
                            ),
                    ),
                ));
    }
}
