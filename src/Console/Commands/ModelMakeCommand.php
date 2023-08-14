<?php

namespace DnSoft\Helper\Console\Commands;

use Illuminate\Console\Command;
use DnSoft\Helper\ModelGenerator;

class ModelMakeCommand extends Command
{
  protected $signature = 'dnsoft:model {name} {module}';

  public function handle()
  {
    $name = $this->argument('name');
    $module = $this->argument('module');

    app(ModelGenerator::class)
      ->setName($name)
      ->setModule($module)
      ->setConsole($this)
      ->generate();
  }
}
