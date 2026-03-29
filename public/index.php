<?php

declare(strict_types=1);

use App\Controller\GraphQL;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->addDefinitions([
    'config.database' => require __DIR__ . '/../config/database/database.php',
    \Doctrine\DBAL\Connection::class => static function (ContainerInterface $container): \Doctrine\DBAL\Connection {
        $params = $container->get('config.database');

        return DriverManager::getConnection($params);
    },
    EntityManager::class => static fn (): EntityManager => require __DIR__ . '/../config/database/doctrine.php',
]);

try {
    $container = $containerBuilder->build();
    $container->get(\Doctrine\DBAL\Connection::class);
} catch (Throwable $throwable) {
    throw new RuntimeException($throwable->getMessage(), 0, $throwable);
}

$dispatcher = FastRoute\simpleDispatcher(static function (FastRoute\RouteCollector $routeCollector): void {
    $routeCollector->post('/graphql', 'graphql.handle');
});

$routeInfo = $dispatcher->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'error' => [
                'message' => 'Route not found.',
            ],
        ]);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        http_response_code(405);
        header('Allow: ' . implode(', ', $allowedMethods));
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'error' => [
                'message' => 'Method not allowed.',
                'allowedMethods' => $allowedMethods,
            ],
        ]);
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        if ($handler === 'graphql.handle') {
            echo $container->get(GraphQL::class)->handle($vars);
            break;
        }

        echo $handler($vars);
        break;
}
