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
        ?string $id,
        StatusEnum $status,
        Customer   $customer,
        array      $productLines = [],
    )
    {
        $this->id = $id ?? IdService::generate();
        $this->status = $status;
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

//    //Commented out intentionally.
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

    // Product lines quantity and unit price are validated during object construction, therefore we don't have to think about it here -- it was done beforehand.
    public function setStatus(StatusEnum $status): void
    {
        // Validating whether the status transition is correct (if draft then we can only send it, if sending then we can only assign status sent-to-client)
        $validTransitions = [
            StatusEnum::Draft->value => [StatusEnum::Sending->value],
            StatusEnum::Sending->value => [StatusEnum::SentToClient->value],
        ];
        if (!isset($validTransitions[$this->status->value]) || !in_array($status->value, $validTransitions[$this->status->value])) {
            throw new \InvalidArgumentException('Invalid status transition');
        }

        $this->status = $status;
    }
    public function getTotalPrice(): Money
    {
        return $this->totalPrice;
    }


}
