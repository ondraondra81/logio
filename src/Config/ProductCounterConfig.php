<?php

declare(strict_types=1);

namespace App\Config;

final class ProductCounterConfig
{
    public function __construct(
        private readonly string $plainTextDirectory,
    ) {
    }

    public function getPlainTextDirectory(): string
    {
        return $this->plainTextDirectory;
    }
}
