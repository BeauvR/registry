<?php

namespace Tests\Unit\Console;

use App\Console\Commands\CreateComposerPackageCommand;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateComposerPackageCommandTest extends TestCase
{
    use WithFaker;

    public function test_the_name_argument_is_required(): void
    {
        $arguments = (new CreateComposerPackageCommand)->getDefinition()->getArguments();

        $this->assertArrayHasKey('name', $arguments);
        $this->assertTrue($arguments['name']->isRequired());
    }

    public function test_the_git_source_argument_is_required(): void
    {
        $arguments = (new CreateComposerPackageCommand)->getDefinition()->getArguments();

        $this->assertArrayHasKey('git_source', $arguments);
        $this->assertTrue($arguments['git_source']->isRequired());
    }

    public function test_the_command_can_create_a_composer_package(): void
    {
        $this->withoutExceptionHandling();

        $this->artisan('create:composer-package', [
            'name' => $name = $this->faker->name,
            'git_source' => $gitSource = $this->faker->url,
        ])->assertExitCode(0);

        $this->assertDatabaseHas('composer_packages', [
            'name' => $name,
            'git_source' => $gitSource,
        ]);
    }
}
