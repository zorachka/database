<?php

declare(strict_types=1);

namespace Zorachka\Framework\Database\Cycle\Migrations\Console\Migration;

use Cycle\Migrations\Migration\Status;
use Cycle\Migrations\MigrationInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Zorachka\Framework\Database\Cycle\Migrations\Event\AfterMigrate;
use Zorachka\Framework\Database\Cycle\Migrations\Event\BeforeMigrate;

final class UpCommand extends BaseMigrationCommand
{
    protected static $defaultName = 'migrations:up';

    public function configure(): void
    {
        $this
            ->setDescription('Execute all new migrations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $migrations = $this->findMigrations($output);
        // check any not executed migration
        $exist = false;
        foreach ($migrations as $migration) {
            if ($migration->getState()->getStatus() === Status::STATUS_PENDING) {
                $exist = true;
                break;
            }
        }
        if (!$exist) {
            $output->writeln('<fg=red>No migration found for execute</>');
            return 0;
        }

        $migrator = $this->migrator;

        // Confirm
        if (!$migrator->getConfig()->isSafe()) {
            $newMigrations = [];
            foreach ($migrations as $migration) {
                if ($migration->getState()->getStatus() === Status::STATUS_PENDING) {
                    $newMigrations[] = $migration;
                }
            }
            $countNewMigrations = count($newMigrations);
            $output->writeln(
                '<fg=yellow>' .
                ($countNewMigrations === 1 ? 'Migration' : $countNewMigrations . ' migrations') .
                ' ' .
                'to be applied:</>'
            );
            foreach ($newMigrations as $migration) {
                $output->writeln('— <fg=cyan>' . $migration->getState()->getName() . '</>');
            }
            $question = new ConfirmationQuestion(
                'Apply the above ' .
                ($countNewMigrations === 1 ? 'migration' : 'migrations') .
                '? (yes|no) ',
                false
            );
            if (!$this->getHelper('question')->ask($input, $output, $question)) {
                return 0;
            }
        }

        $limit = PHP_INT_MAX;
        $this->eventDispatcher->dispatch(new BeforeMigrate());
        try {
            do {
                $migration = $migrator->run();
                if (!$migration instanceof MigrationInterface) {
                    break;
                }

                $state = $migration->getState();
                $status = $state->getStatus();
                $output->writeln('<fg=cyan>' . $state->getName() . '</>: '
                    . (self::MIGRATION_STATUS[$status] ?? $status));
            } while (--$limit > 0);
        } finally {
            $this->eventDispatcher->dispatch(new AfterMigrate());
        }

        return 0;
    }
}
