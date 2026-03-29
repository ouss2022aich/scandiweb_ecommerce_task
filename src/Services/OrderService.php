<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductAttributeItem;
use Doctrine\ORM\EntityManager;
use InvalidArgumentException;

class OrderService
{
    public function __construct(
        private readonly EntityManager $entityManager,
        ?ProductService $productService = null,
    ) {
        $this->productService = $productService ?? new ProductService($this->entityManager);
    }

    private readonly ProductService $productService;

    public function createOrder(array $input): array
    {
        $itemsInput = $input['items'] ?? null;

        if (! is_array($itemsInput) || $itemsInput === []) {
            throw new InvalidArgumentException('Order must contain at least one item.');
        }

        $order = new Order();

        foreach ($itemsInput as $itemInput) {
            $product = $this->findProductBySlug($itemInput['productSlug'] ?? null);
            $quantity = $this->normalizeQuantity($itemInput['quantity'] ?? null);
            $selectedAttributeItemIds = $this->normalizeSelectedAttributeItemIds(
                $product,
                $itemInput['selectedAttributeItemIds'] ?? [],
            );

            $order->addItem(new OrderItem($product, $quantity, $selectedAttributeItemIds));
        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $this->serializeOrder($order);
    }

    public function listOrders(): array
    {
        $orders = $this->entityManager
            ->getRepository(Order::class)
            ->findBy([], ['id' => 'DESC']);

        return array_map(
            fn (Order $order): array => $this->serializeOrder($order),
            $orders,
        );
    }

    public function getOrder(string $id): ?array
    {
        if ($id === '' || ! ctype_digit($id)) {
            throw new InvalidArgumentException('Order id must be a numeric string.');
        }

        $order = $this->entityManager->find(Order::class, (int) $id);

        return $order instanceof Order ? $this->serializeOrder($order) : null;
    }

    public function serializeOrder(Order $order): array
    {
        return [
            'id' => (string) $order->getId(),
            'createdAt' => $order->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'items' => array_map(
                fn (OrderItem $item): array => [
                    'id' => (string) $item->getId(),
                    'quantity' => $item->getQuantity(),
                    'selectedAttributeItemIds' => array_map(
                        static fn (int $attributeItemId): string => (string) $attributeItemId,
                        $item->getSelectedAttributeItemIds(),
                    ),
                    'product' => $this->productService->serializeProduct($item->getProduct()),
                ],
                $order->getItems()->toArray(),
            ),
        ];
    }

    private function findProductBySlug(mixed $slug): Product
    {
        if (! is_string($slug) || $slug === '') {
            throw new InvalidArgumentException('Order item productSlug is required.');
        }

        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['slug' => $slug]);

        if (! $product instanceof Product) {
            throw new InvalidArgumentException(sprintf('Product "%s" was not found.', $slug));
        }

        return $product;
    }

    private function normalizeQuantity(mixed $quantity): int
    {
        if (! is_int($quantity) || $quantity < 1) {
            throw new InvalidArgumentException('Order item quantity must be an integer greater than 0.');
        }

        return $quantity;
    }

    private function normalizeSelectedAttributeItemIds(Product $product, mixed $selectedAttributeItemIds): array
    {
        if (! is_array($selectedAttributeItemIds)) {
            throw new InvalidArgumentException('selectedAttributeItemIds must be an array.');
        }

        $availableAttributeItemIds = array_map(
            static fn (ProductAttributeItem $productAttributeItem): int => $productAttributeItem->getAttributeItem()->getId() ?? 0,
            $product->getAttributeItems()->toArray(),
        );

        $normalizedIds = [];

        foreach ($selectedAttributeItemIds as $selectedAttributeItemId) {
            if (! is_int($selectedAttributeItemId)) {
                throw new InvalidArgumentException('selectedAttributeItemIds values must be integers.');
            }

            if (! in_array($selectedAttributeItemId, $availableAttributeItemIds, true)) {
                throw new InvalidArgumentException(sprintf(
                    'Attribute item "%d" is not valid for product "%s".',
                    $selectedAttributeItemId,
                    $product->getSlug(),
                ));
            }

            $normalizedIds[] = $selectedAttributeItemId;
        }

        return array_values(array_unique($normalizedIds));
    }
}
