<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Command\CommissionsCommand;
use App\Kernel;
use App\Service\CommissionService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CommissionCommandTest extends TestCase
{
    public function testSuccess(): void
    {
        $app = Kernel::getApplication('test');

        $commissionFirst = 0.21;
        $commissionSecond = 0.32;

        $commissionServiceMock = $this->createMock(CommissionService::class);
        $commissionServiceMock->expects($this->once())
            ->method('getTransactionCommissions')
            ->willReturn([
                $commissionFirst,
                $commissionSecond
            ]);

        $command = new CommissionsCommand($commissionServiceMock);
        $app->add($command);
        $app->setAutoExit(false);

        $tester = new CommandTester($app->find('app:commission'));

        $tester->execute([
            'filename' => 'test.txt',
        ]);

        $output = $tester->getDisplay();
        $this->assertStringContainsString( "$commissionFirst\n$commissionSecond\n", $output);
    }
}