<?php

declare(strict_types=1);

namespace App\Command;

use RuntimeException;
use App\Service\CommissionService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:commissions')]
final class CommissionsCommand extends Command
{
    public function __construct(private readonly CommissionService $conversionService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('filename', InputArgument::REQUIRED, 'Transaction source filename');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputFile = $input->getArgument('filename');

        try {
            foreach ($this->conversionService->getTransactionCommissions($inputFile) as $commission) {
                $output->writeln((string) $commission);
            }
        } catch (RuntimeException $e) {
            echo $e->getMessage();
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}