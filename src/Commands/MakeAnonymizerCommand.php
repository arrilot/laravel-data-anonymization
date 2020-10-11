<?php

namespace Arrilot\LaravelDataAnonymization\Commands;

use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\GeneratorCommand;

class MakeAnonymizerCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:anonymizer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new anonymizer class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Anonymizer';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/anonymizer.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        return $this->laravel->databasePath().'/anonymization/'.$name.'.php';
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        return $name;
    }
}
