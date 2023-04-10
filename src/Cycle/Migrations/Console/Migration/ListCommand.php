<?php

declare(strict_types=1);

namespace Zorachka\Database\Cycle\Migrations\Console\Migration;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ListCommand extends BaseMigrationCommand
{
    protected static $defaultName = 'migrations:list';

    public function configure(): void
    {
        $this
            ->setDescription('Print list of all migrations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $list = $this->findMigrations($output);

        foreach ($list as $migration) {
            $state = $migration->getState();
            $output->writeln('<fg=cyan>' . $state->getName() . '</> '
                . '<fg=yellow>[' . (self::MIGRATION_STATUS[$state->getStatus()] ?? '?') . ']</>');
        }

        return Command::SUCCESS;
    }
}
