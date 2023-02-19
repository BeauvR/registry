<?php

namespace Tests\Unit\Policies;

use App\Enums\ComposerPackageVersionType;
use App\Http\Middleware\SetAuthGuard;
use App\Models\ComposerPackage;
use App\Models\License;
use App\Policies\ComposerPackagePolicy;
use Closure;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ComposerPackagePolicyTest extends TestCase
{
    use WithFaker;

    public function test_the_download_method_will_allow_when_the_license_is_linked_to_the_requested_package(): void
    {
        $license = License::factory()
            ->create();
        $composerPackage = ComposerPackage::factory()
            ->create();
        $license->composerPackages()->attach($composerPackage);

        $response = (new ComposerPackagePolicy())->download($license, $composerPackage);

        $this->assertTrue($response->allowed());
    }

    public function test_the_download_method_will_deny_when_the_license_is_linked_the_an_other_package(): void
    {
        $license = License::factory()
            ->create();
        $composerPackage = ComposerPackage::factory()
            ->create();
        $license->composerPackages()->attach($composerPackage);

        $anOtherComposerPackage = ComposerPackage::factory()
            ->create();

        $response = (new ComposerPackagePolicy())->download($license, $anOtherComposerPackage);

        $this->assertTrue($response->denied());
    }

    public function test_the_download_method_will_deny_when_the_license_is_not_linked_to_any_package(): void
    {
        $license = License::factory()
            ->create();
        $composerPackage = ComposerPackage::factory()
            ->create();

        $response = (new ComposerPackagePolicy())->download($license, $composerPackage);

        $this->assertTrue($response->denied());
    }
}
