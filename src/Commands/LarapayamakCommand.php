<?php

namespace Ijeyg\Larapayamak\Commands;

use Illuminate\Console\Command;

class LarapayamakCommand extends Command
{
    public $signature = 'larapayamak';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
