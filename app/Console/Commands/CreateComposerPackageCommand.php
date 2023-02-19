<?php

namespace App\Console\Commands;

use App\Models\ComposerPackage;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(
    name       : 'create:composer-package',
    description: 'With this command you can create a composer package'
)]
class CreateComposerPackageCommand extends Command implements PromptsForMissingInput
{
    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the package (vendor/package)'],
            ['git_source', InputArgument::REQUIRED, 'The git url, make sure the application can pull the repository'],
        ];
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        ComposerPackage::create([
            'name' => $this->argument('name'),
            'git_source' => $this->argument('git_source'),
        ]);

        $this->info('Composer package created');
    }
}
