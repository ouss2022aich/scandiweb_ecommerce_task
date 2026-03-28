<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Attribute;
use App\Models\AttributeItem;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ClothesProduct;
use App\Models\Currency;
use App\Models\GalleryItem;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductAttributeItem;
use App\Models\TechProduct;
use Doctrine\ORM\EntityManager;
use InvalidArgumentException;

class ProductService
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly GalleryUploadService $galleryUploadService = new GalleryUploadService(),
    ) {
    }

    public function listProducts(?string $category = null): array
    {
        $repository = $this->entityManager->getRepository(Product::class);
        $products = $category === null
            ? $repository->findAll()
            : $repository->findBy(['category' => $this->findCategory($category)]);

        return array_map(fn (Product $product): array => $this->serializeProduct($product), $products);
    }

    public function getProduct(string $slug): ?array
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['slug' => $slug]);

        return $product instanceof Product ? $this->serializeProduct($product) : null;
    }

    public function createProduct(array $input, array $uploadedFiles = []): array
    {
        $category = $this->findCategory($input['category']);
        $brand = $this->findOrCreateBrand($input['brand']);
        $product = $this->createProductEntity($input['type'], $input['slug'], $input['name'], $category, $brand);

        $product->setDescription($input['description'] ?? null);
        $product->setInStock($input['inStock'] ?? true);

        foreach ($this->galleryUploadService->validateExistingPaths($input['gallery'] ?? []) as $imageUrl) {
            $product->addGalleryItem(new GalleryItem($imageUrl));
        }

        foreach ($this->galleryUploadService->storeMany($uploadedFiles) as $uploadedImagePath) {
            $product->addGalleryItem(new GalleryItem($uploadedImagePath));
        }

        foreach ($input['prices'] ?? [] as $priceInput) {
            $currency = $this->findCurrency($priceInput['currencyLabel']);
            $product->addPrice(new Price((string) $priceInput['amount'], $currency));
        }

        foreach ($input['attributeItemIds'] ?? [] as $attributeItemId) {
            $attributeItem = $this->findAttributeItem((int) $attributeItemId);
            $product->addAttributeItem(new ProductAttributeItem($attributeItem));
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $this->serializeProduct($product);
    }

    public function serializeProduct(Product $product): array
    {
        return [
            'id' => $product->getSlug(),
            'slug' => $product->getSlug(),
            'type' => $this->resolveProductType($product),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'inStock' => $product->isInStock(),
            'category' => $product->getCategory()->getName(),
            'brand' => $product->getBrand()->getName(),
            'gallery' => array_map(
                static fn (GalleryItem $item): string => $item->getImageUrl(),
                $product->getGalleryItems()->toArray(),
            ),
            'prices' => array_map(
                static fn (Price $price): array => [
                    'amount' => (float) $price->getAmount(),
                    'currency' => [
                        'label' => $price->getCurrency()->getLabel(),
                        'symbol' => $price->getCurrency()->getSymbol(),
                    ],
                ],
                $product->getPrices()->toArray(),
            ),
            'attributes' => $this->groupAttributes($product),
        ];
    }

    private function groupAttributes(Product $product): array
    {
        $grouped = [];

        /** @var ProductAttributeItem $productAttributeItem */
        foreach ($product->getAttributeItems() as $productAttributeItem) {
            $attributeItem = $productAttributeItem->getAttributeItem();
            $attribute = $attributeItem->getAttribute();
            $key = $attribute->getName();

            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'id' => (string) $attribute->getId(),
                    'name' => $attribute->getName(),
                    'type' => $attribute->getType(),
                    'items' => [],
                ];
            }

            $grouped[$key]['items'][] = [
                'id' => (string) $attributeItem->getId(),
                'displayValue' => $attributeItem->getDisplayValue(),
                'value' => $attributeItem->getValue(),
            ];
        }

        return array_values($grouped);
    }

    private function resolveProductType(Product $product): string
    {
        return match (true) {
            $product instanceof TechProduct => 'tech',
            $product instanceof ClothesProduct => 'clothes',
            default => throw new InvalidArgumentException('Unsupported product type.'),
        };
    }

    private function createProductEntity(
        string $type,
        string $slug,
        string $name,
        Category $category,
        Brand $brand,
    ): Product {
        return match ($type) {
            'tech' => new TechProduct($slug, $name, $category, $brand),
            'clothes' => new ClothesProduct($slug, $name, $category, $brand),
            default => throw new InvalidArgumentException(sprintf('Unsupported product type "%s".', $type)),
        };
    }

    private function findCategory(string $name): Category
    {
        $category = $this->entityManager->find(Category::class, $name);

        if (! $category instanceof Category) {
            throw new InvalidArgumentException(sprintf('Category "%s" was not found.', $name));
        }

        return $category;
    }

    private function findOrCreateBrand(string $name): Brand
    {
        $brand = $this->entityManager->getRepository(Brand::class)->findOneBy(['name' => $name]);

        if ($brand instanceof Brand) {
            return $brand;
        }

        $brand = new Brand($name);
        $this->entityManager->persist($brand);

        return $brand;
    }

    private function findCurrency(string $label): Currency
    {
        $currency = $this->entityManager->getRepository(Currency::class)->findOneBy(['label' => $label]);

        if (! $currency instanceof Currency) {
            throw new InvalidArgumentException(sprintf('Currency "%s" was not found.', $label));
        }

        return $currency;
    }

    private function findAttributeItem(int $id): AttributeItem
    {
        $attributeItem = $this->entityManager->find(AttributeItem::class, $id);

        if (! $attributeItem instanceof AttributeItem) {
            throw new InvalidArgumentException(sprintf('Attribute item "%d" was not found.', $id));
        }

        return $attributeItem;
    }
}
