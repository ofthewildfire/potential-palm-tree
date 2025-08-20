<?php

namespace Fuascailtdev\FilamentResourceBuilder\Commands;

use Illuminate\Console\Command;

class FilamentResourceBuilderCommand extends Command
{
    public $signature = 'filament-resource-builder';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
