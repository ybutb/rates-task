<?php

declare(strict_types=1);

use App\Command\CommissionsCommand;
use App\Service\BinProvider\BinProviderInterface;
use App\Service\BinProvider\DefaultBinProvider;
use App\Service\CommissionService;
use App\Service\RatesProvider\DefaultRatesProvider;
use App\Service\RatesProvider\RatesProviderInterface;
use App\Storage\TransactionStorage;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

$containerBuilder = new ContainerBuilder();

$containerBuilder->register(CommissionService::class, CommissionService::class)->setAutowired(true);
$containerBuilder->register(BinProviderInterface::class, DefaultBinProvider::class)
    ->setArguments([
        new Reference('bin_api_url')
    ]);

$containerBuilder->register(RatesProviderInterface::class, DefaultRatesProvider::class)
    ->setArguments([
        new Reference('rates_api_url'),
        '%env(RATES_API_TOKEN)%'
    ]);

$containerBuilder->register(TransactionStorage::class, TransactionStorage::class)
    ->setArguments([
        './data'
    ]);

$containerBuilder->register('rates_api_url', HttpClientInterface::class)
    // the first argument is the class and the second argument is the static method
    ->setFactory([HttpClient::class, 'createForBaseUri'])
    ->setArguments([
        '%env(RATES_API_URL)%',
    ]);

$containerBuilder->register('bin_api_url', HttpClientInterface::class)
    // the first argument is the class and the second argument is the static method
    ->setFactory([HttpClient::class, 'createForBaseUri'])
    ->setArguments([
        '%env(BIN_API_URL)%',
    ]);

$containerBuilder->register('rates_api_url', HttpClientInterface::class)
    // the first argument is the class and the second argument is the static method
    ->setFactory([HttpClient::class, 'createForBaseUri'])
    ->setArguments([
        '%env(RATES_API_URL)%',
    ]);



$containerBuilder->register(CommissionsCommand::class, CommissionsCommand::class)
    ->setAutowired(true)
    ->setPublic(true);

$containerBuilder->compile(true);

return $containerBuilder;
