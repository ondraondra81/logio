<?php

declare(strict_types=1);

namespace App\Product\Service\Cache;

use App\Contracts\IProductProvider;
use App\Contracts\IProductRepository;
use Psr\SimpleCache\CacheInterface;

final class CachedProductRepository implements IProductRepository
{
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly IProductProvider $productProvider,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function findById(string $id): array
    {
        $cacheKey = $this->createCacheKey($id);

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $product = $this->productProvider->findById($id);

        $this->cache->set($cacheKey, $product);

        return $product;
    }

    private function createCacheKey(string $id): string
    {
        return sprintf('product.%s', $id);
    }
}
