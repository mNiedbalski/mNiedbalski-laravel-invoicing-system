<?php

namespace Invoices\Domain;

use Modules\Invoices\Domain\Entities\Customer;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\ProductLine;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\ValueObjects\IdService;
use Modules\Invoices\Domain\ValueObjects\Money;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class InvoiceTest extends TestCase
{
    public function test_invoice_creation_with_draft_status(): void
    {
        $customer = new Customer('John Doe', 'john.doe@example.com');
        $invoice = new Invoice($customer);

        $this->assertEquals(StatusEnum::Draft, $invoice->getStatus());
    }

    public function test_mark_as_sending(): void
    {
        $customer = new Customer('John Doe', 'john.doe@example.com');
        $productLine1 = new ProductLine(name: 'Running shoes', quantity: 1, unitPrice: new Money(19900));
        $productLine2 = new ProductLine(name: 'Water bottle', quantity: 3, unitPrice: new Money(200));
        $invoice = new Invoice($customer, StatusEnum::Draft, [$productLine1, $productLine2]);

        $invoice->markAsSending();

        $this->assertEquals(StatusEnum::Sending, $invoice->getStatus());
    }

    public function test_mark_as_sending_with_invalid_status(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Invoice must be in draft status to be sent.');

        $customer = new Customer('John Doe', 'john.doe@example.com');
        $productLine1 = new ProductLine(name: 'Running shoes', quantity: 1, unitPrice: new Money(19900));
        $productLine2 = new ProductLine(name: 'Water bottle', quantity: 3, unitPrice: new Money(200));
        $invoice = new Invoice($customer, StatusEnum::SentToClient, [$productLine1, $productLine2], IdService::generate()); // If ID is not assigned to Invoice, status will be set to Draft by default (Explained in Invoice Entity class)

        $invoice->markAsSending();
    }

    public function test_mark_as_sent_to_client(): void
    {
        $customer = new Customer('John Doe', 'john.doe@example.com');
        $productLine1 = new ProductLine(name: 'Running shoes', quantity: 1, unitPrice: new Money(19900));
        $productLine2 = new ProductLine(name: 'Water bottle', quantity: 3, unitPrice: new Money(200));
        $invoice = new Invoice($customer, StatusEnum::Sending, [$productLine1, $productLine2], IdService::generate()); // If ID is not assigned to Invoice, status will be set to Draft by default (Explained in Invoice Entity class)

        $invoice->markAsSentToClient();

        $this->assertEquals(StatusEnum::SentToClient, $invoice->getStatus());
    }

    public function test_mark_as_sent_to_client_with_invalid_status(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Invoice must be in sending status to be marked as sent-to-client.');

        $customer = new Customer('John Doe', 'john.doe@example.com');
        $productLine1 = new ProductLine(name: 'Running shoes', quantity: 1, unitPrice: new Money(19900));
        $productLine2 = new ProductLine(name: 'Water bottle', quantity: 3, unitPrice: new Money(200));
        $invoice = new Invoice($customer, StatusEnum::Draft, [$productLine1, $productLine2], IdService::generate()); // If ID is not assigned to Invoice, status will be set to Draft by default (Explained in Invoice Entity class)

        $invoice->markAsSentToClient();
    }

    public function test_total_price_is_calculated_correctly(): void
    {
        $customer = new Customer('John Doe', 'john.doe@example.com');
        $productLine1 = new ProductLine(name: 'Running shoes', quantity: 1, unitPrice: new Money(19900));
        $productLine2 = new ProductLine(name: 'Water bottle', quantity: 3, unitPrice: new Money(200));
        $productLine3 = new ProductLine(name: 'Shirt', quantity: 2, unitPrice: new Money(5000));

        $invoice = new Invoice($customer, productLines: [$productLine1, $productLine2, $productLine3]);

        $expectedTotalPrice = 19900 + (3 * 200) + (2 * 5000);
        $this->assertEquals($expectedTotalPrice, $invoice->getTotalPrice()->getAmount());
    }
    public function test_sending_with_product_lines_empty(): void {

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Invoice must have at least one product line.');

        $customer = new Customer('John Doe', 'john.doe@example.com');
        $invoice = new Invoice($customer);
        $invoice->markAsSending();
    }

}
