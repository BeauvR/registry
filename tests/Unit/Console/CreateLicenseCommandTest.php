<?php

namespace Tests\Unit\Console;

use App\Console\Commands\CreateLicenseCommand;
use App\Models\License;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateLicenseCommandTest extends TestCase
{
    use WithFaker;

    public function test_the_username_argument_is_required(): void
    {
        $arguments = (new CreateLicenseCommand())->getDefinition()->getArguments();

        $this->assertArrayHasKey('username', $arguments);
        $this->assertTrue($arguments['username']->isRequired());
    }

    public function test_the_password_argument_is_required(): void
    {
        $arguments = (new CreateLicenseCommand())->getDefinition()->getArguments();

        $this->assertArrayHasKey('password', $arguments);
        $this->assertTrue($arguments['password']->isRequired());
    }

    public function test_the_command_can_create_a_license(): void
    {
        $this->withoutExceptionHandling();

        $this->artisan('create:license', [
            'username' => $username = $this->faker->name,
            'password' => 'password',
        ])->assertExitCode(0);

        $this->assertDatabaseHas('licenses', [
            'username' => $username,
        ]);

        $createdLicensePassword = License::query()
            ->where('username', $username)
            ->first()
            ->password;

        $this->assertTrue(Hash::check('password', $createdLicensePassword));
    }
}
