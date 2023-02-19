<?php

namespace Tests\Unit\Console;

use App\Console\Commands\CreateLicenseCommand;
use App\Console\Commands\LinkComposerPackageToLicenseCommand;
use App\Models\ComposerPackage;
use App\Models\License;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LinkComposerPackageToLicenseCommandTest extends TestCase
{
    use WithFaker;

    public function test_the_packages_names_argument_is_required(): void
    {
        $arguments = (new LinkComposerPackageToLicenseCommand())->getDefinition()->getArguments();

        $this->assertArrayHasKey('package_names', $arguments);
        $this->assertTrue($arguments['package_names']->isRequired());
    }

    public function test_the_license_username_argument_is_required(): void
    {
        $arguments = (new LinkComposerPackageToLicenseCommand())->getDefinition()->getArguments();

        $this->assertArrayHasKey('license_username', $arguments);
        $this->assertTrue($arguments['license_username']->isRequired());
    }

    public function test_the_package_names_must_exists(): void
    {
        $license = License::factory()->create();

        $this->artisan('link:composer-package-to-license', [
            'package_names' => 'package1,package2',
            'license_username' => $license->username,
        ])->assertFailed();
    }

    public function test_each_individual_package_must_exists(): void
    {
        $license = License::factory()->create();

        $package = ComposerPackage::factory()->create();

        $this->artisan('link:composer-package-to-license', [
            'package_names' => $package . ',package2',
            'license_username' => $license->username,
        ])->assertFailed();
    }

    public function test_the_license_username_must_exists(): void
    {
        $package = ComposerPackage::factory()->create();

        $this->artisan('link:composer-package-to-license', [
            'package_names' => $package->name,
            'license_username' => 'username',
        ])->assertFailed();
    }

    public function test_the_command_can_link_a_package_to_a_license(): void
    {
        $this->withoutExceptionHandling();

        $license = License::factory()->create();

        $package = ComposerPackage::factory()->create();

        $this->artisan('link:composer-package-to-license', [
            'package_names' => $package->name,
            'license_username' => $license->username,
        ])->assertOk();

        $this->assertDatabaseHas('composer_package_license', [
            'composer_package_id' => $package->id,
            'license_id' => $license->id,
        ]);
    }

    public function test_the_command_can_link_multiple_packages_to_a_license(): void
    {
        $this->withoutExceptionHandling();

        $license = License::factory()->create();

        $package1 = ComposerPackage::factory()->create();
        $package2 = ComposerPackage::factory()->create();

        $this->artisan('link:composer-package-to-license', [
            'package_names' => $package1->name . ',' . $package2->name,
            'license_username' => $license->username,
        ])->assertOk();

        $this->assertDatabaseHas('composer_package_license', [
            'composer_package_id' => $package1->id,
            'license_id' => $license->id,
        ]);

        $this->assertDatabaseHas('composer_package_license', [
            'composer_package_id' => $package2->id,
            'license_id' => $license->id,
        ]);
    }
}
