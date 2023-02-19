<?php

namespace App\Console\Commands;

use App\Models\ComposerPackage;
use App\Models\License;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(
    name       : 'create:license',
    description: 'With this command you can create a license'
)]
class CreateLicenseCommand extends Command implements PromptsForMissingInput
{
    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['username', InputArgument::REQUIRED, 'The username of the license, which is used to login'],
            ['password', InputArgument::REQUIRED, 'The password of the license, which is used to login'],
        ];
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        License::create([
            'username' => $this->argument('username'),
            'password' => Hash::make($this->argument('password')),
        ]);

        $this->info('License created');
    }
}
