<?php

declare(strict_types=1);

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$connectionParams = require __DIR__ . '/../database/database.php';

if (($connectionParams['host'] ?? null) === 'mysql' && ! is_file('/.dockerenv')) {
    $connectionParams['host'] = '127.0.0.1';
    $connectionParams['port'] = 3307;
}

$config = ORMSetup::createAttributeMetadataConfig(
    paths: [__DIR__ . '/../../src/Models'],
    isDevMode: ($_ENV['APP_ENV'] ?? 'dev') !== 'prod',
    cacheNamespaceSeed: __DIR__ . '/../../src/Models',
    cache: new ArrayAdapter(),
);
$proxyDir = sys_get_temp_dir() . '/scandiweb_doctrine_proxies';

if (! is_dir($proxyDir)) {
    mkdir($proxyDir, 0777, true);
}

$config->setProxyDir($proxyDir);
$config->setProxyNamespace('DoctrineProxies');
$config->setAutoGenerateProxyClasses(true);

$entityManager = new EntityManager(
    DriverManager::getConnection($connectionParams, $config),
    $config,
);

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    ConsoleRunner::run(new SingleManagerProvider($entityManager));

    return null;
}

return $entityManager;
