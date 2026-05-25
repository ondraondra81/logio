<?php

declare(strict_types=1);

namespace App\Product\Handler;

use App\Contracts\IProductQueryCounter;
use App\Contracts\IProductRepository;

final class ProductDetailHandler
{
    public function __construct(
        private readonly IProductRepository $productRepository,
        private readonly IProductQueryCounter $productQueryCounter,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function handle(string $id): array
    {
        $product = $this->productRepository->findById($id);

        $this->productQueryCounter->increment($id);

        return $product;
    }
}
