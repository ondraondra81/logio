<?php

declare(strict_types=1);

namespace App\Product\Service\Counter;

use App\Config\ProductCounterConfig;
use App\Contracts\IProductQueryCounter;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;

final class PlainTextProductQueryCounter implements IProductQueryCounter
{
    public function __construct(
        private readonly ProductCounterConfig $config,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function increment(string $productId): void
    {
        $file = null;
        $locked = false;
        $filePath = null;

        try {
            $directory = $this->createDirectoryPath($productId);

            if (!is_dir($directory)) {
                mkdir($directory, 0775, true);
            }

            $filePath = $this->createFilePath($directory, $productId);
            $file = fopen($filePath, 'c+');

            if ($file === false) {
                throw new RuntimeException(sprintf('Could not open counter file "%s".', $filePath));
            }

            $locked = flock($file, LOCK_EX);

            if (!$locked) {
                throw new RuntimeException(sprintf('Could not lock counter file "%s".', $filePath));
            }

            $contents = stream_get_contents($file);
            $queryCount = $this->parseCount($contents === false ? '' : $contents);
            $queryCount++;

            rewind($file);
            ftruncate($file, 0);
            fwrite($file, sprintf('%d%s', $queryCount, PHP_EOL));
            fflush($file);
        } catch (Throwable $exception) {
            $this->logger->warning('Plain text product query counter increment failed.', [
                'product_id' => $productId,
                'directory' => $this->config->getPlainTextDirectory(),
                'file_path' => $filePath,
                'exception' => $exception,
            ]);
        } finally {
            if (is_resource($file)) {
                if ($locked) {
                    flock($file, LOCK_UN);
                }

                fclose($file);
            }
        }
    }

    private function createDirectoryPath(string $productId): string
    {
        return sprintf(
            '%s/%d',
            rtrim($this->config->getPlainTextDirectory(), '/'),
            $this->createBucketId($productId),
        );
    }

    private function createBucketId(string $productId): int
    {
        return intdiv(max(0, (int) $productId), 1000) * 1000;
    }

    private function createFilePath(string $directory, string $productId): string
    {
        return sprintf(
            '%s/%s.txt',
            rtrim($directory, '/'),
            rawurlencode($productId),
        );
    }

    private function parseCount(string $contents): int
    {
        return max(0, (int) trim($contents));
    }
}
