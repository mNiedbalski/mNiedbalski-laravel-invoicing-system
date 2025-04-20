<?php

namespace Modules\Invoices\Domain\Entities;

use DateTimeImmutable;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\ValueObjects\IdService;
use Modules\Invoices\Domain\ValueObjects\Money;

/**
 * Class Invoice
 *
 * Represents an invoice in the system.
 *
 * <p>At first I have added methods responsible for adding product lines and removing then, but then I realized that since it is Invoice,
 * productLines array should be immutable; So I have commented them out.
 * </p>
 *
 * @property string $id
 * @property StatusEnum $status
 * @property Customer $customer
 * @property array $productLines
 * @property Money $totalPrice
 */
class Invoice
{
    private string $id;
    private StatusEnum $status;
    private Customer $customer;
    private array $productLines = [];
    private Money $totalPrice;

    /** Constructor that creates invoice object.
     * @param string $id
     * @param StatusEnum $status
     * @param Customer $customer
     * @param array $productLines
     */
    public function __construct(
        Customer   $customer,
        array      $productLines = [],
        ?string $id = null,
        StatusEnum $status = StatusEnum::Draft,
    )
    {
        $this->id = $id ?? IdService::generate();

        // If ID is not provided then we assume it's a new invoice and set status to Draft. If id was provided, then it is known that Invoice is read from DB.
        $this->status = $id ? $status : StatusEnum::Draft;
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
    /** Marking invoice as sending with status transition validation.
     * @return void
     */
    public function markAsSending(): void
    {
        if ($this->status !== StatusEnum::Draft) {
            throw new \DomainException('Invoice must be in draft status to be sent.');
        }
        $this->status = StatusEnum::Sending;
    }

    /** Marking invoice as sent to client with status transition validation.
     * @return void
     */
    public function markAsSentToClient(): void
    {
        if ($this->status !== StatusEnum::Sending) {
            throw new \DomainException('Invoice must be in sending status to be marked as sent-to-client.');
        }
        $this->status = StatusEnum::SentToClient;
    }


    // OPTIONAL EXPANSION
    /**
     * Tax is calculated on each of the product line items, because tax might differ between different products.
     * @return Money
     */
    public function getTotalTaxedAmount(): Money
    {
        $totalTaxedAmount = new Money(0); // Initialize with zero

        /** @var ProductLine $productLine */
        foreach ($this->productLines as $productLine) {
            $totalTaxedAmount = $totalTaxedAmount->add($productLine->getTotalTaxedAmount());
        }
        return $totalTaxedAmount;
    }

    /**
     * Discount is calculated on each of the product line items, because discount might differ between different products.
     * @return Money
     */
    public function getTotalDiscountedAmount(): Money
    {
        $totalDiscountedAmount = new Money(0); // Initialize with zero

        /** @var ProductLine $productLine */
        foreach ($this->productLines as $productLine) {
            $totalDiscountedAmount = $totalDiscountedAmount->add($productLine->getTotalDiscountedAmount());
        }
        return $totalDiscountedAmount;
    }

    /**
     * Tax and discount are calculated on each of the product line items, because they might differ between different products.
     * @return Money
     */
    public function getTotalTaxedAndDiscountedAmount(): Money
    {
        $totalTaxedAndDiscountedAmount = new Money(0); // Initialize with zero

        /** @var ProductLine $productLine */
        foreach ($this->productLines as $productLine) {
            $totalTaxedAndDiscountedAmount = $totalTaxedAndDiscountedAmount->add($productLine->getTotalDiscountedAndTaxedAmount());
        }
        return $totalTaxedAndDiscountedAmount;
    }
    // END OF OPTIONAL EXPANSION

//    //Commented out intentionally because at the stage of creating invoice productLines array is immutable.
//    public function addProductLine(ProductLine $productLine): void
//    {
//        $this->productLines[] = $productLine;
//        $this->totalPrice = $this->totalPrice->add($productLine->getTotalUnitPrice());
//        $this->updatedAt = new DateTimeImmutable();
//    }
//    public function removeProductLine(ProductLine $productLine): void
//    {
//        $this->productLines = array_filter(
//            $this->productLines,
//            fn($line) => $line !== $productLine
//        );
//
//        $this->totalPrice = $this->totalPrice->subtract($productLine->getTotalUnitPrice());
//        $this->updatedAt = new DateTimeImmutable();
//    }


    // Getters and setters
    public function getProductLines(): array
    {
        return $this->productLines;
    }
    public function getId(): string
    {
        return $this->id;
    }
    public function getCustomer(): Customer
    {
        return $this->customer;
    }
    public function getStatus(): StatusEnum
    {
        return $this->status;
    }
    public function getTotalPrice(): Money
    {
        return $this->totalPrice;
    }


}
