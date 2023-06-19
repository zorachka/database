<?php

declare(strict_types=1);

namespace Zorachka\Database\Cycle\Migrations\Console\Migration;

use Cycle\Migrations\MigrationInterface;
use Cycle\Migrations\State;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Zorachka\Database\Event\AfterMigrate;
use Zorachka\Database\Event\BeforeMigrate;

final class DownCommand extends BaseMigrationCommand
{
    protected static $defaultName = 'migrations:down';

    public function configure(): void
    {
        $this
            ->setDescription('Rollback last migration');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $migrations = $this->findMigrations($output);
        // check any executed migration
        foreach (array_reverse($migrations) as $migration) {
            if ($migration->getState()->getStatus(
            ) === State::STATUS_EXECUTED) {
                $exist = true;
                break;
            }
        }
        if (!isset($migration)) {
            $output->writeln('<fg=red>No migration found for rollback</>');
            return 0;
        }

        // Confirm
        if (!$this->migrator->getConfig()->isSafe()) {
            $output->writeln('<fg=yellow>Migration to be reverted:</>');
            $output->writeln(
                '— <fg=cyan>' . $migration->getState()->getName() . '</>'
            );
            $question = new ConfirmationQuestion(
                'Revert the above migration? (yes|no) ',
                false
            );
            /** @phpstan-ignore-next-line */
            if (!$this->getHelper('question')->ask(
                $input,
                $output,
                $question
            )) {
                return 0;
            }
        }

        $this->eventDispatcher->dispatch(new BeforeMigrate());
        try {
            $this->migrator->rollback();
            if (!$migration instanceof MigrationInterface) {
                throw new Exception('Migration not found');
            }

            $state = $migration->getState();
            $status = $state->getStatus();
            $output->writeln(
                sprintf(
                    '<fg=cyan>%s</>: %s',
                    $state->getName(),
                    self::MIGRATION_STATUS[$status] ?? $status
                )
            );
        } finally {
            $this->eventDispatcher->dispatch(new AfterMigrate());
        }

        return Command::SUCCESS;
    }
}
