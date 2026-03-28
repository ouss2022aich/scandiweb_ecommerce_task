<?php

use Doctrine\DBAL\DriverManager;
use Psr\Container\ContainerInterface;

require_once __DIR__ . '/../vendor/autoload.php';

// load env vars
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


$container_builder = new \DI\ContainerBuilder();
$container_builder->addDefinitions([
    'config.database' => require __DIR__ . '/../config/database/database.php',
    \Doctrine\DBAL\Connection::class => function (ContainerInterface $c): \Doctrine\DBAL\Connection{
      $params = $c->get('config.database');
      return DriverManager::getConnection($params);
    },
]);

try {
    $container = $container_builder->build();
    $container->get(\Doctrine\DBAL\Connection::class);

} catch (Exception $e) {
    throw new Exception($e->getMessage());
}



$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->post('/graphql', [App\Controller\GraphQL::class, 'handle']);
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
        echo $handler($vars);
        break;
}
