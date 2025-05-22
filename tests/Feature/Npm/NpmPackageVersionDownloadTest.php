<?php

namespace Tests\Feature\Npm;

use App\Models\NpmPackageVersion;
use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class NpmPackageVersionDownloadTest extends TestCase
{
    use WithFaker;

    public function test_the_endpoint_is_not_public_accessible(): void
    {
        $version = NpmPackageVersion::factory()->create();

        $this->getJson(route('npm.npmPackage.npmPackageVersion.download', [$version->npmPackage, $version]))
            ->assertUnauthorized()
            ->assertHeader('WWW-Authenticate', 'Basic');
    }

    public function test_the_endpoint_can_not_be_accessed_when_user_authentication_is_used(): void
    {
        $version = NpmPackageVersion::factory()->create();
        $user = User::factory()->create();

        $this->withHeaders([
            'Authorization' => 'Basic ' . base64_encode($user->email . ':password'),
        ])->getJson(route('npm.npmPackage.npmPackageVersion.download', [$version->npmPackage, $version]))
            ->assertUnauthorized();
    }

    public function test_the_endpoint_can_be_accessed_when_license_authentication_is_used_and_the_license_is_linked(): void
    {
        $version = NpmPackageVersion::factory()->create();
        $license = License::factory()->create();
        $license->npmPackages()->attach($version->npmPackage);

        Storage::shouldReceive('exists')->andReturn(false);

        $this->getJson(route('npm.npmPackage.npmPackageVersion.download', [$version->npmPackage, $version]), [
            'Authorization' => 'Basic ' . base64_encode($license->username . ':password'),
        ])->assertNotFound();
    }
}
