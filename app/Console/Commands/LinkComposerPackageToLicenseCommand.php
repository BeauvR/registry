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
    name       : 'link:composer-package-to-license',
    description: 'With this command you can link a composer package to a license'
)]
class LinkComposerPackageToLicenseCommand extends Command implements PromptsForMissingInput
{
    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['package_names', InputArgument::REQUIRED, 'A list of packages you can to link to the license (delimited by a comma, ex: vendor/package1,vendor/package2)'],
            ['license_username', InputArgument::REQUIRED, 'The username of the license you want to link the packages to'],
        ];
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $license = License::query()
            ->where('username', $this->argument('license_username'))
            ->first();

        if (!$license) {
            $this->error('License not found');
            return Command::FAILURE;
        }

        $packageNames = explode(',', $this->argument('package_names'));
        $packages = ComposerPackage::query()
            ->whereIn('name', $packageNames)
            ->get();

        if ($packages->count() !== count($packageNames)) {
            $this->error('Not all packages were found');
            return Command::FAILURE;
        }

        $license->composerPackages()
             ->syncWithoutDetaching($packages->pluck('id'));

        return Command::SUCCESS;
    }
}
