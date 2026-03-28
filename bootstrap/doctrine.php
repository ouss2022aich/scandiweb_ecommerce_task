<?php

declare(strict_types=1);

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$connectionParams = require __DIR__ . '/../config/database.php';

if (($connectionParams['host'] ?? null) === 'mysql') {
    $connectionParams['host'] = '127.0.0.1';
    $connectionParams['port'] = 3307;
}

$config = ORMSetup::createAttributeMetadataConfig(
    paths: [__DIR__ . '/../src/Models'],
    isDevMode: ($_ENV['APP_ENV'] ?? 'dev') !== 'prod',
    cacheNamespaceSeed: __DIR__ . '/../src/Models',
    cache: new ArrayAdapter(),
);
$config->setProxyDir(__DIR__ . '/../var/doctrine/proxies');
$config->setProxyNamespace('DoctrineProxies');
$config->setAutoGenerateProxyClasses(true);

$connection = DriverManager::getConnection(
    $connectionParams,
    $config,
);

return new EntityManager($connection, $config);
