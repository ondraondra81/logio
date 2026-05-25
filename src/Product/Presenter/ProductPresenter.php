<?php

declare(strict_types=1);

namespace App\Product\Presenter;

use App\Product\Handler\ProductDetailHandler;
use JsonException;
use Psr\Log\LoggerInterface;
use Throwable;

final class ProductPresenter
{
    public function __construct(
        private readonly ProductDetailHandler $productDetailHandler,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function detail(string $id): string
    {
        try {
            $product = $this->productDetailHandler->handle($id);

            return json_encode($product, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            $this->logger->error('Product detail response serialization failed.', [
                'product_id' => $id,
                'exception' => $exception,
            ]);

            return $this->createErrorResponse();
        } catch (Throwable $exception) {
            $this->logger->error('Product detail failed.', [
                'product_id' => $id,
                'exception' => $exception,
            ]);

            return $this->createErrorResponse();
        }
    }

    private function createErrorResponse(): string
    {
        return json_encode([
            'error' => 'Product detail could not be loaded.',
        ]) ?: '{"error":"Product detail could not be loaded."}';
    }
}
