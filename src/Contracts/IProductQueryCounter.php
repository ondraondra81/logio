<?php

declare(strict_types=1);

namespace App\Contracts;

interface IProductQueryCounter
{
    public function increment(string $productId): void;
}
