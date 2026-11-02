<?php

namespace App\Services\AiSeo;

interface AiSeoDriver
{
    public function generate(string $productName, string $description, string $specs, ?string $targetKeyword = null): AiSeoPayload;
}
