<?php

use App\Models\CapacityAttribute;
use App\Models\ClothingCategory;
use App\Models\ColorAttribute;
use App\Models\SizeAttribute;
use App\Models\TechCategory;

return [

    TechCategory::class => [
        ColorAttribute::class,
        SizeAttribute::class,
        CapacityAttribute::class,
    ],

    ClothingCategory::class => [
        ColorAttribute::class,
        SizeAttribute::class,
    ],

];