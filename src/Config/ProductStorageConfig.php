<?php

declare(strict_types=1);

namespace App\Config;

final class ProductStorageConfig
{
    public function __construct(
        private readonly bool $elasticSearchEnabled,
    ) {
    }

    public function isElasticSearchEnabled(): bool
    {
        return $this->elasticSearchEnabled;
    }
}
