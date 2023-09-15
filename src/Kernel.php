<?php

declare(strict_types=1);

namespace App;

use App\Command\CommissionsCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;
use Symfony\Component\Dotenv\Dotenv;

final class Kernel
{
    public static function getApplication($env = 'local'): Application
    {
        (new Dotenv())->bootEnv(dirname(__DIR__).'/.env.' . $env);

        $container = require __DIR__.'./../config/config.php';

        $application = new Application();

        $commandLoader = new ContainerCommandLoader($container, [
            'app:commissions' => CommissionsCommand::class,
        ]);

        $application->setCommandLoader($commandLoader);
        $application->setDefaultCommand('app:commissions', true);

        return $application;
    }
}
