<?php

declare(strict_types=1);

namespace App\Factories\Attribute;

use App\Models\Attribute;
use InvalidArgumentException;

class AttributeFactory
{
    /**
     * @param AttributeCreatorInterface[] $creators
     */
    public function __construct(
        private readonly array $creators = [
            new SizeAttributeCreator(),
            new ColorAttributeCreator(),
            new CapacityAttributeCreator(),
            new TextAttributeCreator(),
        ],
    ) {
    }

    public function create(string $name, string $type): Attribute
    {
        foreach ($this->creators as $creator) {
            if ($creator->supports($name, $type)) {
                return $creator->create($name);
            }
        }

        throw new InvalidArgumentException(sprintf(
            'Unsupported attribute "%s" with type "%s".',
            $name,
            $type,
        ));
    }
}
