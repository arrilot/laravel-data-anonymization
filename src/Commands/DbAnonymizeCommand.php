<?php

namespace Arrilot\LaravelDataAnonymization\Commands;

use Arrilot\DataAnonymization\Anonymizer as CoreAnonymizer;
use Arrilot\LaravelDataAnonymization\AbstractAnonymizer;
use Arrilot\DataAnonymization\Database\SqlDatabase;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Input\InputOption;

class DbAnonymizeCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'db:anonymize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Anonymize the database';

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['class', null, InputOption::VALUE_OPTIONAL, 'The class name of the root anonymizer', 'DatabaseAnonymizer'],

            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to anonymize'],

            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        $this->getAnonymizer()->run();
    }

    /**
     * Get an anonymizer instance from the container.
     *
     * @return AbstractAnonymizer
     */
    protected function getAnonymizer()
    {
        $className = $this->input->getOption('class');

        return (new $className($this->getCoreAnonymizer()))->setCommand($this);
    }

    /**
     * Get core anonymizer from parent package.
     *
     * @return CoreAnonymizer
     */
    protected function getCoreAnonymizer()
    {
        $db = $this->getDatabaseConfiguration($this->input->getOption('database'));
        $databaseInteractor = new SqlDatabase($db['dsn'], $db['user'], $db['password']);

        return new CoreAnonymizer($databaseInteractor);
    }

    /**
     * Get database configuration from laravel config
     *
     * @param string $selected
     *
     * @return array
     */
    protected function getDatabaseConfiguration($selected)
    {
        $database = $selected ?: $this->laravel['config']['database.default'];

        $connection = $this->laravel['config']['database.connections.'.$database];

        return [
            'dsn' => "{$connection['driver']}:dbname={$connection['database']};host={$connection['host']};charset={$connection['charset']}",
            'user' => $connection['user'],
            'password' => $connection['password'],
        ];
    }
}
