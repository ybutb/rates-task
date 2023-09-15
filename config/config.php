<?php

declare(strict_types=1);

use App\Command\CommissionsCommand;
use App\Service\BinProvider\BinDataProviderInterface;
use App\Service\BinProvider\DefaultBinDataProvider;
use App\Service\CommissionService;
use App\Service\FileHandler;
use App\Service\RatesProvider\DefaultRatesProvider;
use App\Service\RatesProvider\RatesProviderInterface;
use App\Storage\TransactionStorage;
use App\Storage\TransactionStorageInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

$containerBuilder = new ContainerBuilder();

$containerBuilder->register(CommissionService::class, CommissionService::class)->setAutowired(true);
$containerBuilder->register(BinDataProviderInterface::class, DefaultBinDataProvider::class)
    ->setArguments([
        new Reference('bin_api_client')
    ]);

$containerBuilder->register(RatesProviderInterface::class, DefaultRatesProvider::class)
    ->setArguments([
        new Reference('rates_api_client')
    ]);

$containerBuilder->register(FileHandler::class, FileHandler::class);
$containerBuilder->register(TransactionStorageInterface::class, TransactionStorage::class)
    ->setArguments([
        new Reference(FileHandler::class),
        './data'
    ]);

$containerBuilder->register('bin_api_client', HttpClientInterface::class)
    ->setFactory([HttpClient::class, 'createForBaseUri'])
    ->setArguments([
        '%env(BIN_API_URL)%',
    ]);

$containerBuilder->register('rates_api_client', HttpClientInterface::class)
    ->setFactory([HttpClient::class, 'createForBaseUri'])
    ->setArguments([
        '%env(RATES_API_URL)%',
        [
            'query' => [
                'access_key' => '%env(RATES_API_TOKEN)%',
            ],
        ]
    ]);

$containerBuilder->register(CommissionsCommand::class, CommissionsCommand::class)
    ->setAutowired(true)
    ->setPublic(true);

$containerBuilder->compile(true);

return $containerBuilder;
