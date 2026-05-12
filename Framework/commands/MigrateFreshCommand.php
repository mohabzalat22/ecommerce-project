<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'migrate:fresh', description: 'Drop all tables and re-run all migrations')]
class MigrateFreshCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $databaseBootstrap = defined('BASE_PATH')
                ? BASE_PATH.'/bootstrap/database.php'
                : dirname(__DIR__, 2).'/bootstrap/database.php';

            if (!is_file($databaseBootstrap) || !is_readable($databaseBootstrap)) {
                throw new RuntimeException('Database bootstrap file is missing or unreadable: '.$databaseBootstrap);
            }

            require_once $databaseBootstrap;

            $output->writeln('<comment>Dropping all tables...</comment>');
            Capsule::connection()->getSchemaBuilder()->dropAllTables();
            $output->writeln('<info>Database wiped.</info>');

            return (new MigrateCommand())->run(new ArrayInput([]), $output);
        } catch (Throwable $e) {
            $errorOutput = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
            $message = $e->getMessage();
            $previous = $e->getPrevious();
            if (null !== $previous && '' !== $previous->getMessage()) {
                $message .= ' | Cause: '.$previous->getMessage();
            }

            $errorOutput->writeln('<error>migrate:fresh failed:</error> '.$message);

            return Command::FAILURE;
        }
    }
}
