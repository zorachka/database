<?php

declare(strict_types=1);

namespace Zorachka\Database\Cycle\Migrations\Console\Migration;

use Cycle\Database\DatabaseManager;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\Exception\RepositoryException;
use Cycle\Migrations\MigrationInterface;
use Cycle\Migrations\Migrator;
use Cycle\Migrations\State;
use Cycle\Schema\Generator\Migrations\MigrationImage;
use Exception;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseMigrationCommand extends Command
{
    protected const MIGRATION_STATUS = [
        State::STATUS_UNDEFINED => 'undefined',
        State::STATUS_PENDING => 'pending',
        State::STATUS_EXECUTED => 'executed',
    ];
    protected DatabaseManager $manager;
    protected MigrationConfig $migrationConfig;
    protected Migrator $migrator;
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(
        DatabaseManager $manager,
        MigrationConfig $migrationConfig,
        Migrator $migrator,
        EventDispatcherInterface $eventDispatcher,
    ) {
        $this->manager = $manager;
        $this->migrationConfig = $migrationConfig;
        $this->migrator = $migrator;
        $this->eventDispatcher = $eventDispatcher;
        parent::__construct();
    }

    protected function createEmptyMigration(
        OutputInterface $output,
        string $name,
        ?string $database = null
    ): ?MigrationImage {
        if ($database === null) {
            // get default database
            $database = $this->manager->database()->getName();
        }
        $migrator = $this->migrator;

        $migrationSkeleton = new MigrationImage($this->migrationConfig, $database);
        $migrationSkeleton->setName($name);
        try {
            $migrationFile = $migrator->getRepository()->registerMigration(
                $migrationSkeleton->buildFileName(),
                /** @phpstan-ignore-next-line */
                $migrationSkeleton->getClass()->getName(),
                $migrationSkeleton->getFile()->render()
            );
        } catch (RepositoryException $e) {
            $output->writeln('<fg=yellow>Can not create migration</>');
            $output->writeln('<fg=red>' . $e->getMessage() . '</>');

            return null;
        }
        $output->writeln('<info>New migration file has been created</info>');
        $output->writeln("<fg=cyan>{$migrationFile}</>");

        return $migrationSkeleton;
    }

    /**
     * @return MigrationInterface[]
     * @throws Exception
     */
    protected function findMigrations(OutputInterface $output): array
    {
        $list = $this->migrator->getMigrations();
        $output->writeln(
            sprintf(
                '<info>Total %d migration(s) found in %s</info>',
                count($list),
                $this->migrationConfig->getDirectory()
            )
        );
        return $list;
    }
}
