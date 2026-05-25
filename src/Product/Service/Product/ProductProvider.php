<?php

declare(strict_types=1);

namespace App\Product\Service\Product;

use App\Config\ProductStorageConfig;
use App\Contracts\IElasticSearchDriver;
use App\Contracts\IMySQLDriver;
use App\Contracts\IProductProvider;
use Throwable;

final class ProductProvider implements IProductProvider
{
    public function __construct(
        private readonly ProductStorageConfig $config,
        private readonly IElasticSearchDriver $elasticSearchDriver,
        private readonly IMySQLDriver $mySQLDriver,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function findById(string $id): array
    {
        if ($this->config->isElasticSearchEnabled()) {
            try {
                return $this->elasticSearchDriver->findById($id);
            } catch (Throwable) {
                return $this->mySQLDriver->findProduct($id);
            }
        }

        return $this->mySQLDriver->findProduct($id);
    }
}
