<?php

declare(strict_types=1);

namespace Zorachka\Database\Cycle\Migrations\Console\Migration;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateCommand extends BaseMigrationCommand
{
    protected static $defaultName = 'migrations:create';

    public function configure(): void
    {
        $this->setDescription('Create an empty migration')
             ->setHelp('This command allows you to create a custom migration')
             ->addArgument('name', InputArgument::REQUIRED, 'Migration name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $customName */
        $customName = $input->getArgument('name');

        $this->createEmptyMigration($output, $customName);

        return Command::SUCCESS;
    }
}
