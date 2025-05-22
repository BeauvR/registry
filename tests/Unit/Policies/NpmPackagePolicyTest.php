<?php

namespace Tests\Unit\Policies;

use App\Models\License;
use App\Models\NpmPackage;
use App\Policies\NpmPackagePolicy;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class NpmPackagePolicyTest extends TestCase
{
    use WithFaker;

    public function test_the_download_method_will_allow_when_the_license_is_linked_to_the_requested_package(): void
    {
        $license = License::factory()->create();
        $npmPackage = NpmPackage::factory()->create();
        $license->npmPackages()->attach($npmPackage);

        $response = (new NpmPackagePolicy())->download($license, $npmPackage);

        $this->assertTrue($response->allowed());
    }

    public function test_the_download_method_will_deny_when_the_license_is_linked_to_an_other_package(): void
    {
        $license = License::factory()->create();
        $npmPackage = NpmPackage::factory()->create();
        $license->npmPackages()->attach($npmPackage);

        $anOtherPackage = NpmPackage::factory()->create();

        $response = (new NpmPackagePolicy())->download($license, $anOtherPackage);

        $this->assertTrue($response->denied());
    }

    public function test_the_download_method_will_deny_when_the_license_is_not_linked_to_any_package(): void
    {
        $license = License::factory()->create();
        $npmPackage = NpmPackage::factory()->create();

        $response = (new NpmPackagePolicy())->download($license, $npmPackage);

        $this->assertTrue($response->denied());
    }
}
