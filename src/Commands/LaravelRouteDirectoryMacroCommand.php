<?php

namespace AntoninMasek\LaravelRouteDirectoryMacro\Commands;

use Illuminate\Console\Command;

class LaravelRouteDirectoryMacroCommand extends Command
{
    public $signature = 'laravel-route-directory-macro';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
