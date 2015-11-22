<?php

namespace Arrilot\LaravelDataAnonymization;

use Illuminate\Console\Command;
use Arrilot\DataAnonymization\Anonymizer as CoreAnonymizer;

abstract class AbstractAnonymizer
{
    /**
     * The console command instance.
     *
     * @var \Illuminate\Console\Command
     */
    protected $command;

    /**
     * Core anonymizer.
     *
     * @var \Arrilot\DataAnonymization\Anonymizer
     */
    protected $core;

    /**
     * AbstractAnonymizer constructor.
     *
     * @param \Arrilot\DataAnonymization\Anonymizer $core
     */
    public function __construct(CoreAnonymizer $core)
    {
        $this->core = $core;
    }

    /**
     * Run the anonymization.
     *
     * @return void
     */
    abstract public function run();

    /**
     * Call selected anonymizer.
     *
     * @param string $class
     *
     * @return void
     */
    public function call($class)
    {
        $this->resolve($class)->run();

        if (isset($this->command)) {
            $this->command->getOutput()->writeln("<info>Anonymized:</info> $class");
        }
    }

    /**
     * Describe a table with a given callback.
     *
     * @param string   $name
     * @param callable $callback
     *
     * @return void
     */
    public function table($name, callable $callback)
    {
        $this->core->table($name, $callback);
    }

    /**
     * Set the console command instance.
     *
     * @param  \Illuminate\Console\Command  $command
     *
     * @return $this
     */
    public function setCommand(Command $command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Resolve an instance of the given seeder class.
     *
     * @param  string  $class
     *
     * @return AbstractAnonymizer
     */
    protected function resolve($class)
    {
        $instance = new $class($this->core);

        if (isset($this->command)) {
            $instance->setCommand($this->command);
        }

        return $instance;
    }
}
