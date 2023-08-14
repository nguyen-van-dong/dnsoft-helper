<?php

namespace DnSoft\Helper;

use DnSoft\Helper\Exceptions\GeneratorException;
use Illuminate\Console\Command as Console;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ModuleGenerator
{
    protected $name;

    /**
     * @var Console
     */
    protected $console;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    protected $folders = [
        'config',
        'database/migrations',
        'resources/views',
        'resources/lang',
        'src/Http/Controllers/Admin',
        'src/Http/Controllers/Web',
        'src/Http/Middleware',
        'src/Http/Requests',
        'src/Models',
        'src/Repositories/Eloquents',
        'src/Repositories/Cache',
        'src/Console/Commands',
        'src/Providers',
    ];

    protected $files = [
        'module.json.stub'       => 'module.json',
        'module-provider.stub'   => 'src/__STUDLY_NAME__ServiceProvider.php',
        'routes/admin.stub'      => 'routes/admin.php',
        'routes/web.stub'        => 'routes/web.php',
        'controllers/admin.stub' => 'src/Http/Controllers/Admin/__STUDLY_NAME__Controller.php',
        'controllers/web.stub'   => 'src/Http/Controllers/Web/__STUDLY_NAME__Controller.php',
        'views/admin.stub'       => 'resources/views/admin/test-__KEBAB_NAME__/index.blade.php',
        'views/web.stub'         => 'resources/views/web/test-__KEBAB_NAME__/index.blade.php',
        // 'model.stub'             => 'src/Models/__STUDLY_NAME__.php',
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

    /**
     * @throws \Exception
     */
    public function generate()
    {
        if (!$this->name) {
            throw new GeneratorException('Name is missing');
        }

        $this->generateFolders();
        $this->generateFiles();

        $this->console->info(sprintf('Module %s created', $this->name));
        $this->console->info('Add new line to modules.json');
        $this->console->warn(sprintf("{\n    ...\n    \"%s\": true\n}", $this->getKebabName()));
    }

    protected function getModulePath()
    {
        return base_path('modules' . '/' . $this->getKebabName());
    }

    protected function generateFolders()
    {
        foreach ($this->folders as $folder) {
            $path = $this->getModulePath() . '/' . $folder;

            $this->filesystem->makeDirectory($path, 0755, true);
        }
    }

    protected function generateFiles()
    {
        foreach ($this->files as $stub => $path) {
            $path = $this->getModulePath() . '/' . $this->replacement($path);

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
        ], [
            $this->getStudlyName(),
            $this->getKebabName(),
            $this->getLowerName(),
            $this->name,
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
        return Str::lower($this->name);
    }

    private function getStubContent($stub)
    {
        $content = $this->filesystem->get(__DIR__ . '/../stubs/' . $stub);

        return $this->replacement($content);
    }
}
