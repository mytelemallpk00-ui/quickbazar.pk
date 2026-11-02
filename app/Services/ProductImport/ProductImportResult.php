<?php

namespace App\Services\ProductImport;

class ProductImportResult
{
    /** @param  list<array{row?: int|string, message: string}>  $failures */
    public function __construct(
        public int $total = 0,
        public int $created = 0,
        public int $updated = 0,
        public array $failures = [],
    ) {}

    public function failedCount(): int
    {
        return count($this->failures);
    }
}
