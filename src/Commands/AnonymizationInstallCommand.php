<?php

namespace Arrilot\LaravelDataAnonymization\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Input\InputOption;

class AnonymizationInstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'anonymization:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install anonymization boilerplate';

    /**
     * The Composer instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The Composer instance.
     *
     * @var Composer
     */
    protected $composer;

    /**
     * Constructor.
     *
     * @param Filesystem $files
     * @param Composer $composer
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    public function handle()
    {
    	return $this->fire();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $dir = $this->laravel->databasePath() . '/anonymization';

        $this->createDirectory($dir);
        $this->createAnonymizer($dir, 'DatabaseAnonymizer');
        $this->createAnonymizer($dir, 'UserTableAnonymizer');

        $this->composer->dumpAutoloads();

        $this->info("Installation completed");

    }

    /**
     * Create directory for anonymizers.
     *
     * @param string $dir
     */
    protected function createDirectory($dir)
    {
        if ($this->files->isDirectory($dir)) {
            $this->error("Directory {$dir} already exists");

            return;
        }

        $this->files->makeDirectory($dir);
    }

    /**
     * Create Anonymizer class.
     *
     * @param string $dir
     *
     * @return void
     */
    protected function createAnonymizer($dir, $class)
    {
        $path = "{$dir}/{$class}.php";

        if ($this->files->exists($path)) {
            $this->error("File {$path} already exists");

            return;
        }

        $this->files->copy(__DIR__.'/stubs/'.$class.'.stub', $path);
    }
}
