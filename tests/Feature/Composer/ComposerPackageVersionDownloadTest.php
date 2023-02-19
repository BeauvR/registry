<?php

namespace Tests\Feature\Composer;

use App\Enums\ComposerPackageVersionType;
use App\Models\ComposerPackage;
use App\Models\ComposerPackageVersion;
use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ComposerPackageVersionDownloadTest extends TestCase
{
    use WithFaker;

    public function test_the_endpoint_is_not_public_accessible(): void
    {
        $composerPackageVersion = ComposerPackageVersion::factory()
            ->create();

        $this->getJson(
            route(
                'composer.composerPackage.composerPackageVersion.download',
                [$composerPackageVersion->composerPackage, $composerPackageVersion],
            ),
        )
            ->assertUnauthorized()
            ->assertHeader('WWW-Authenticate', 'Basic');
    }

    public function test_the_endpoint_can_not_be_accessed_when_user_authentication_is_used(): void
    {
        $composerPackageVersion = ComposerPackageVersion::factory()
            ->create();

        $user = User::factory()
            ->create();

        $this->withHeaders([
            'Authorization' => 'Basic ' . base64_encode($user->email . ':password'),
        ])->getJson(
            route(
                'composer.composerPackage.composerPackageVersion.download',
                [$composerPackageVersion->composerPackage, $composerPackageVersion],
            ),
        )
            ->assertUnauthorized()
            ->assertHeader('WWW-Authenticate', 'Basic');
    }

    public function test_the_endpoint_can_not_be_accessed_when_license_authentication_is_used_but_the_license_is_not_linked(
    ): void
    {
        $composerPackageVersion = ComposerPackageVersion::factory()
            ->create();

        $license = License::factory()
            ->create();

        $this->getJson(
            route(
                'composer.composerPackage.composerPackageVersion.download',
                [$composerPackageVersion->composerPackage, $composerPackageVersion],
            ), [
            'Authorization' => 'Basic ' . base64_encode($license->username . ':password'),
        ],
        )
            ->assertForbidden();
    }

    public function test_the_endpoint_can_be_accessed_when_license_authentication_is_used_and_the_license_is_linked(): void
    {
        $composerPackageVersion = ComposerPackageVersion::factory()
            ->create();
        $license = License::factory()
            ->create();
        $license->composerPackages()
            ->attach($composerPackageVersion->composerPackage);

        $this->getJson(
            route(
                'composer.composerPackage.composerPackageVersion.download',
                [$composerPackageVersion->composerPackage, $composerPackageVersion],
            ), [
            'Authorization' => 'Basic ' . base64_encode($license->username . ':password'),
        ],
        )
            ->assertNotFound()
            ->assertJson(
                fn (AssertableJson $json) => $json->has('error')
                    ->where('error', 'Package version dist not found'),
            );
    }

    public function test_the_endpoint_returns_404_when_the_storage_path_does_not_exist(): void
    {
        $composerPackageVersion = ComposerPackageVersion::factory()
            ->create();
        $license = License::factory()
            ->create();
        $license->composerPackages()
            ->attach($composerPackageVersion->composerPackage);

        Storage::shouldReceive('exists')
            ->withArgs(
                fn (string $path) => $path === $composerPackageVersion->storage_path,
            )
            ->once()
            ->andReturn(false);

        $this->getJson(
            route(
                'composer.composerPackage.composerPackageVersion.download',
                [$composerPackageVersion->composerPackage, $composerPackageVersion],
            ), [
            'Authorization' => 'Basic ' . base64_encode($license->username . ':password'),
        ],
        )
            ->assertNotFound()
            ->assertJson(
                fn (AssertableJson $json) => $json->has('error')
                    ->where('error', 'Package version dist not found'),
            );
    }

    public function test_the_endpoint_returns_the_download_correctly(): void
    {
        $composerPackageVersion = ComposerPackageVersion::factory()
            ->create();
        $license = License::factory()
            ->create();
        $license->composerPackages()
            ->attach($composerPackageVersion->composerPackage);

        Storage::shouldReceive('exists')
            ->withArgs(
                fn (string $path) => $path === $composerPackageVersion->storage_path,
            )
            ->once()
            ->andReturn(true);
        Storage::shouldReceive('mimeType')
            ->withArgs(
                fn (string $path) => $path === $composerPackageVersion->storage_path,
            )
            ->once()
            ->andReturn('application/zip');
        Storage::shouldReceive('get')
            ->withArgs(
                fn (string $path) => $path === $composerPackageVersion->storage_path,
            )
            ->once()
            ->andReturn('test');

        $file_name = Str::afterLast($composerPackageVersion->storage_path, '/');

        $this->getJson(
            route(
                'composer.composerPackage.composerPackageVersion.download',
                [$composerPackageVersion->composerPackage, $composerPackageVersion],
            ), [
            'Authorization' => 'Basic ' . base64_encode($license->username . ':password'),
        ],
        )
            ->assertOk()
            ->assertHeader('Content-Type', 'application/zip')
            ->assertHeader('Content-Disposition', 'attachment; filename="' . $file_name . '"')
            ->assertSee('test');
    }
}
