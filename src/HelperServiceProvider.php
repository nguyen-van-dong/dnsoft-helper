<?php

namespace DnSoft\Helper;

use DnSoft\Helper\Console\Commands\ModelMakeCommand;
use Illuminate\Support\ServiceProvider;
use DnSoft\Helper\Console\Commands\ModuleMakeCommand;

class HelperServiceProvider extends ServiceProvider
{
    protected $commandsCli = [
        ModuleMakeCommand::class,
        ModelMakeCommand::class,
    ];

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands($this->commandsCli);
        }
    }
}
