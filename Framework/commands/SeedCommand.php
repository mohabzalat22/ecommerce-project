<?php

declare(strict_types=1);

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'seed', description: 'Run database seeders')]
class SeedCommand extends Command
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

            $seedersPath = dirname(__DIR__, 2).'/database/seeders';

            if (!is_dir($seedersPath)) {
                throw new RuntimeException('Seeders directory not found: '.$seedersPath);
            }

            // Load EcommerceSeeder
            $seederFile = $seedersPath.'/EcommerceSeeder.php';
            if (!is_file($seederFile) || !is_readable($seederFile)) {
                throw new RuntimeException('Seeder file not found or not readable: '.$seederFile);
            }

            require_once $seederFile;

            $output->writeln('<info>Running seeders...</info>');

            $seeder = new EcommerceSeeder();
            $seeder->run();

            $output->writeln('<fg=green>✓ Seed data imported successfully!</fg=green>');

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $output->writeln("<error>Error: {$e->getMessage()}</error>");

            if ($output instanceof ConsoleOutputInterface) {
                $output->getErrorOutput()->writeln("<error>Trace:\n{$e->getTraceAsString()}</error>");
            }

            return Command::FAILURE;
        }
    }
}
