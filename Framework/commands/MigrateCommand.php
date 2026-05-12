<?php

declare(strict_types=1);

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'migrate', description: 'Run database migrations')]
class MigrateCommand extends Command
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

            $migrationsPath = dirname(__DIR__, 2).'/database/migrations';

            if (!is_dir($migrationsPath)) {
                mkdir($migrationsPath);
            }

            $files = glob($migrationsPath.'/*.php');
            if (false === $files) {
                throw new RuntimeException("Failed to read migrations from: {$migrationsPath}");
            }

            sort($files); // run in timestamp order

            if (0 === count($files)) {
                $output->writeln('<comment>No migration files found.</comment>');

                return Command::SUCCESS;
            }

            foreach ($files as $file) {
                if (!is_readable($file)) {
                    throw new RuntimeException('Migration file is not readable: '.basename($file));
                }

                try {
                    $migration = require $file;
                } catch (Throwable $e) {
                    throw new RuntimeException('Failed loading migration: '.basename($file), 0, $e);
                }

                if (is_array($migration) && isset($migration['up']) && is_callable($migration['up'])) {
                    try {
                        $migration['up']();
                    } catch (Throwable $e) {
                        throw new RuntimeException('Migration up() callback failed: '.basename($file), 0, $e);
                    }
                } elseif (is_object($migration) && method_exists($migration, 'up')) {
                    try {
                        $migration->up();
                    } catch (Throwable $e) {
                        throw new RuntimeException('Migration up() method failed: '.basename($file), 0, $e);
                    }
                } else {
                    throw new UnexpectedValueException(
                        'Invalid migration format in '.basename($file).'. Expected array with callable up or object with up() method.'
                    );
                }

                $output->writeln('<info>Migrated:</info> '.basename($file));
            }

            $output->writeln('<info>All migrations ran successfully.</info>');

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $errorOutput = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
            $message = $e->getMessage();
            $previous = $e->getPrevious();
            if (null !== $previous && '' !== $previous->getMessage()) {
                $message .= ' | Cause: '.$previous->getMessage();
            }

            $errorOutput->writeln('<error>Migration failed:</error> '.$message);

            return Command::FAILURE;
        }
    }
}
