<?php

namespace App;

use App\Command\CommissionsCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;

class Kernel
{
    public static function getApplication(): Application
    {
        $container = require __DIR__.'./../config/config.php';

        $application = new Application();

        $commandLoader = new ContainerCommandLoader($container, [
            'app:commission' => CommissionsCommand::class,
        ]);

        $application->setCommandLoader($commandLoader);
        $application->setDefaultCommand('app:commission', true);

        return $application;
    }
}
