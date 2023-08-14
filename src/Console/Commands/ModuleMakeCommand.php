<?php

namespace DnSoft\Helper\Console\Commands;

use Illuminate\Console\Command;
use DnSoft\Helper\ModuleGenerator;

class ModuleMakeCommand extends Command
{
    protected $signature = 'dnsoft:module {name}';

    public function handle()
    {
        $name = $this->argument('name');

        app(ModuleGenerator::class)
            ->setName($name)
            ->setConsole($this)
            ->generate();
    }
}
