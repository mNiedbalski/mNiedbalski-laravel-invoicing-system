<?php

namespace Invoices\Domain;

use Modules\Invoices\Domain\Entities\ProductLine;
use Modules\Invoices\Domain\ValueObjects\Money;
use PHPUnit\Framework\TestCase;

class ProductLineTest extends TestCase
{
    public function test_product_line_creation_with_valid_data(): void
    {
        $productLine = new ProductLine('Test Product', 2, new Money(1000));

        $this->assertEquals('Test Product', $productLine->getName());
        $this->assertEquals(2, $productLine->getQuantity());
        $this->assertEquals(2000, $productLine->getTotalUnitPrice()->getAmount());
    }

    public function test_product_line_creation_with_invalid_quantity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity of item Test Product must be a positive integer greater than zero.');

        new ProductLine('Test Product', 0, new Money(1000));
    }

    public function test_product_line_creation_with_invalid_unit_price(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unit price of item Test Product must be a positive integer greater than zero.');

        new ProductLine('Test Product', 2, new Money(0));
    }
}
