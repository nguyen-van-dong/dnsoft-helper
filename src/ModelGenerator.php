<?php

namespace DnSoft\Helper;

use DnSoft\Helper\Exceptions\GeneratorException;
use Illuminate\Console\Command as Console;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ModelGenerator
{
  protected $name;

  protected $module;

  /**
   * @var Console
   */
  protected $console;

  /**
   * @var Filesystem
   */
  protected $filesystem;

  protected $files = [
    'request.stub' => 'src/Http/Requests/__STUDLY_NAME__Request.php',
    'controllers/admin.stub' => 'src/Http/Controllers/Admin/__STUDLY_NAME__Controller.php',
    'repository.stub' => 'src/Repositories/Eloquents/__STUDLY_NAME__Repository.php',
    'repository-interface.stub' => 'src/Repositories/__STUDLY_NAME__RepositoryInterface.php',
    'views/admin/index.stub' => 'resources/views/admin/__KEBAB_NAME__/index.blade.php',
    'views/admin/create.stub' => 'resources/views/admin/__KEBAB_NAME__/create.blade.php',
    'views/admin/edit.stub' => 'resources/views/admin/__KEBAB_NAME__/edit.blade.php',
    'views/admin/__fields.stub' => 'resources/views/admin/__KEBAB_NAME__/__fields.blade.php',
    'model.stub' => 'src/Models/__STUDLY_NAME__.php',
  ];

  /**
   * ModuleGenerator constructor.
   *
   * @param Filesystem $filesystem
   */
  public function __construct(Filesystem $filesystem)
  {
    $this->filesystem = $filesystem;
  }

  public function setConsole(Console $console)
  {
    $this->console = $console;

    return $this;
  }

  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  public function setModule($module)
  {
    $this->module = $module;

    return $this;
  }

  /**
   * @throws \Exception
   */
  public function generate()
  {
    if (!$this->name || !$this->module) {
      throw new GeneratorException('Name or module is missing');
    }

    $this->generateFiles();

    $this->console->info(sprintf('Model %s created in module %s', $this->name, $this->module));
  }

  protected function getModulePath()
  {
    return base_path('modules' . '/' . $this->getKebabName());
  }

  protected function getFilePath()
  {
    return base_path('modules' . '/' . $this->module);
  }

  protected function generateFiles()
  {
    foreach ($this->files as $stub => $path) {
      $path = $this->getFilePath() . '/' . $this->replacement($path);

      if (!$this->filesystem->isDirectory($dir = dirname($path))) {
        $this->filesystem->makeDirectory($dir, 0775, true);
      }

      $this->filesystem->put($path, $this->getStubContent($stub));
    }
  }

  private function replacement($content)
  {
    return str_replace([
      '__STUDLY_NAME__',
      '__KEBAB_NAME__',
      '__LOWER_NAME__',
      '__MODULE_NAME__',
      '__MODEL_NAME__',
      '__MODEL_NAME_UPPER__',
      '__MODULE_NAME_UPPER__',
    ], [
      $this->getStudlyName(),
      $this->getKebabName(),
      $this->getLowerName(),
      $this->module,
      $this->getLowerModelName(),
      $this->name,
      $this->getUpperModuleName()
    ], $content);
  }

  private function getStudlyName()
  {
    return Str::studly($this->name);
  }

  private function getKebabName()
  {
    return Str::kebab($this->name);
  }

  private function getLowerName()
  {
    return Str::lower($this->module);
  }

  private function getUpperModuleName()
  {
    return Str::ucfirst($this->module);
  }

  private function getLowerModelName()
  {
    return Str::lower($this->name);
  }

  private function getStubContent($stub)
  {
    $content = $this->filesystem->get(__DIR__ . '/../stubs/' . $stub);

    return $this->replacement($content);
  }
}
