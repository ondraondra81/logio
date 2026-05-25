<?php

declare(strict_types=1);

namespace App\Contracts;

interface IMySQLDriver
{
    /**
     * @return array<string, mixed>
     */
    public function findProduct(string $id): array;
}
