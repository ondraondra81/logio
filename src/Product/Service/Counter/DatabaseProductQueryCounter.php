<?php

declare(strict_types=1);

namespace App\Product\Service\Counter;

use App\Contracts\IProductQueryCounter;
use PDO;
use Psr\Log\LoggerInterface;
use Throwable;

final class DatabaseProductQueryCounter implements IProductQueryCounter
{
    public function __construct(
        private readonly PDO $connection,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function increment(string $productId): void
    {
        try {
            // Requires a unique key on product_query_stats.product_id.
            $statement = $this->connection->prepare(
                'INSERT INTO product_query_stats (product_id, query_count) VALUES (:product_id, 1) '
                . 'ON DUPLICATE KEY UPDATE query_count = query_count + 1',
            );

            $statement->execute([
                'product_id' => $productId,
            ]);
        } catch (Throwable $exception) {
            $this->logger->warning('Product query counter increment failed.', [
                'product_id' => $productId,
                'exception' => $exception,
            ]);
        }
    }
}
