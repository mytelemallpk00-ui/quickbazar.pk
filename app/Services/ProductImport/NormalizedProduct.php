<?php

namespace App\Services\ProductImport;

class NormalizedProduct
{
    public function __construct(
        public string $name,
        public float $price,
        public float $oldPrice,
        public string $description,
        public string $image,
        public ?string $hoverImage = null,
        /** @var list<string> */
        public array $gallery = [],
        public ?string $externalSku = null,
        public ?string $sourceUrl = null,
        public ?string $categoryName = null,
        public float $rating = 0,
        public int $reviews = 0,
        public bool $inStock = true,
    ) {}
}
