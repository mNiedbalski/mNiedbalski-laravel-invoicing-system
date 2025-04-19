<?php

namespace Modules\Invoices\Domain\Entities;

use Modules\Invoices\Domain\ValueObjects\IdService;
use Modules\Invoices\Domain\ValueObjects\Money;
use Ramsey\Uuid\Uuid;

/**
 * Class ProductLine
 * Represents a product line item in an invoice.
 *
 * I intentionally left subtractQuantity and addQuantity methods commented, because in this task I am preparing final logic of the quoting process.
 * From business point of view, Invoice is a final product given to the client after preparing the quotation, and item quantities and prices are defined before progressing to this step.
 *
 * I have also decided to not include setters for the properties, because they are not necessary for the task at hand and in the Invoice generation context, productLines are immutable.
 * If I was to implement a full invoicing system with quoting and order management, I would add them because they would be used in the quoting process.
 * I've also decided to add taxRate and discountRate as additional properties. They weren't mentioned in the task, but they are common in invoicing systems.
 */
class ProductLine
{
    private string $id; // string because UUID
    private string $name;
    private int $quantity;
    private Money $unitPrice;
    private Money $totalUnitPrice;

    //optional expansion
    private float $taxRate = 0.0;
    private float $discountRate = 0.0;


    public function __construct(string $name, int $quantity, Money $unitPrice, float $taxRate = 0.0, float $discountRate = 0.0, ?string $id = null )
    {
        //optional expansion
        if ($taxRate < 0 || $taxRate > 100) {
            throw new \InvalidArgumentException('Tax rate must be between 0 and 100.');
        }

        if ($discountRate < 0 || $discountRate > 100) {
            throw new \InvalidArgumentException('Discount rate must be between 0 and 100.');
        }
        //end of optional expansion

        $this->id = $id ?? IdService::generate();
        $this->name = $name;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->totalUnitPrice = new Money($unitPrice->getAmount() * $quantity);

        //optional expansion
        $this->taxRate = $taxRate;
        $this->discountRate = $discountRate;
        //end of optional expansion
    }

    public function getTotalUnitPrice(): Money
    {
        return $this->totalUnitPrice;
    }

    //Commented out intentionally.
//    public function subtractQuantity(int $quantity): void
//    {
//        if ($this->quantity < $quantity) {
//            throw new \InvalidArgumentException('Cannot subtract more than available quantity.');
//        }
//        $this->quantity -= $quantity;
//        $this->totalUnitPrice = new Money($this->unitPrice->getAmount() * $this->quantity);
//    }
//    public function addQuantity(int $quantity): void
//    {
//        $this->quantity += $quantity;
//        $this->totalUnitPrice = new Money($this->unitPrice->getAmount() * $this->quantity);
//    }

    // Those Getters aren't necessary yet, but they might come in handy in the future in displaying the invoice
    public function getName(): string
    {
        return $this->name;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getUnitPrice(): Money
    {
        return $this->unitPrice;
    }
    public function getId(): string
    {
        return $this->id;
    }
    // Optional methods I decided to add to the class

    public function getTaxAmount(): Money
    {
        $taxAmount = (int) round($this->totalUnitPrice->getAmount() * ($this->taxRate / 100));
        return new Money($taxAmount);
    }

    public function getTotalTaxedAmount(): Money
    {
        return $this->totalUnitPrice->add($this->getTaxAmount());
    }

    public function getDiscountAmount(): Money
    {
        $discountAmount = (int) round($this->totalUnitPrice->getAmount() * ($this->discountRate / 100));
        return new Money($discountAmount);
    }
    public function getTotalDiscountedAmount(): Money
    {
        return $this->totalUnitPrice->subtract($this->getDiscountAmount());
    }

    public function getTotalDiscountedAndTaxedAmount(): Money
    {
        $discountedPrice = $this->getTotalDiscountedAmount();
        $taxAmount = (int) round($discountedPrice->getAmount() * ($this->taxRate / 100));
        return $discountedPrice->add(new Money($taxAmount));
    }

    // End of optional methods

}
