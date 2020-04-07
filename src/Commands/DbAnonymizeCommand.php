<?php

namespace Arrilot\LaravelDataAnonymization\Commands;

use Arrilot\DataAnonymization\Anonymizer as CoreAnonymizer;
use Arrilot\LaravelDataAnonymization\AbstractAnonymizer;
use Arrilot\DataAnonymization\Database\SqlDatabase;
use Faker\Generator as FakerGenerator;
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
        if (! $this->confirmToProceed()) {
            return;
        }

        $coreAnonymizer = $this->getCoreAnonymizer();

        // collect configuration into $coreAnonymizer
        $this->getAnonymizer($coreAnonymizer)->run();

        // change database
        $coreAnonymizer->run();
    }

    /**
     * Get an anonymizer instance from the container.
     *
     * @param CoreAnonymizer $coreAnonymizer
     *
     * @return AbstractAnonymizer
     */
    protected function getAnonymizer($coreAnonymizer)
    {
        $className = $this->input->getOption('class');

        return (new $className($coreAnonymizer))->setCommand($this);
    }

    /**
     * Get core anonymizer from parent package.
     *
     * @return CoreAnonymizer
     */
    protected function getCoreAnonymizer()
    {
        $db = $this->getDatabaseConfiguration($this->input->getOption('database'));

        $databaseInteractor = new SqlDatabase($db['dsn'], $db['username'], $db['password']);
        $generator = $this->laravel->make(FakerGenerator::class);

        return new CoreAnonymizer($databaseInteractor, $generator);
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

        $host = $connection['host'] ?? $connection['write']['host'][0] ?? '127.0.0.1';

        return [
            'dsn' => "{$connection['driver']}:dbname={$connection['database']};host={$host};port={$connection['port']};charset={$connection['charset']}",
            'username' => $connection['username'],
            'password' => $connection['password'],
        ];
    }
}
