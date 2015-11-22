<?php

namespace Arrilot\LaravelDataAnonymization\Commands;

use Illuminate\Console\Command;
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
}
