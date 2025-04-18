<?php

namespace Modules\Invoices\Domain\Entities;

use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use Modules\Invoices\Domain\ValueObjects\Money;

class Invoice
{
    private InvoiceId $id;
    private StatusEnum $status;
    private Customer $customer;
    private array $productLines = [];
    private Money $totalPrice;

    /** Constructor that creates invoice object.
     * @param InvoiceId $id
     * @param StatusEnum $status
     * @param Customer $customer
     * @param array $productLines
     */
    public function __construct(InvoiceId $id, StatusEnum $status, Customer $customer, array $productLines = [])
    {
        $this->id = $id;
        $this->status = StatusEnum::Draft;
        $this->customer = $customer;
        $this->productLines = $productLines;
        $this->calculateTotalPrice();
    }
    private function calculateTotalPrice(): void
    {
        $this->totalPrice = new Money(0); // Initialize with zero

        foreach ($this->productLines as $productLine) {
            $this->totalPrice = $this->totalPrice->add($productLine->getTotalUnitPrice());
        }
    }
    public function addProductLine(ProductLine $productLine): void
    {
        $this->productLines[] = $productLine;
        $this->totalPrice = $this->totalPrice->add($productLine->getTotalUnitPrice());
    }
    public function removeProductLine(ProductLine $productLine): void
    {
        $this->productLines = array_filter(
            $this->productLines,
            fn($line) => $line !== $productLine
        );

        $this->totalPrice = $this->totalPrice->subtract($productLine->getTotalUnitPrice());
    }

    // Getters and setters
    public function getStatus(): StatusEnum
    {
        return $this->status;
    }

    public function setStatus(StatusEnum $status): void
    {
        $this->status = $status;
    }
    public function getTotalPrice(): Money
    {
        return $this->totalPrice;
    }
}
