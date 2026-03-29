<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers;

use App\Services\OrderService;

class OrderResolver
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {
    }

    public function resolveOrders(): array
    {
        return $this->orderService->listOrders();
    }

    public function resolveOrder(array $args): ?array
    {
        return $this->orderService->getOrder($args['id']);
    }

    public function resolveCreateOrder(array $args): array
    {
        return $this->orderService->createOrder($args['input']);
    }
}
