<?php

namespace Invoices\Domain;

use Modules\Invoices\Domain\Entities\Customer;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\ProductLine;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\ValueObjects\Money;
use PHPUnit\Framework\TestCase;

class InvoiceTest extends TestCase
{
    public function test_invoice_creation_with_draft_status(): void
    {
        $customer = new Customer('John Doe', 'john.doe@example.com', '12345');
        $invoice = new Invoice('123', StatusEnum::Draft, $customer, []);

        $this->assertEquals(StatusEnum::Draft, $invoice->getStatus());
    }

    public function test_mark_as_sending(): void
    {
        $customer = new Customer('John Doe', 'john.doe@example.com', '12345');
        $invoice = new Invoice('123', StatusEnum::Draft, $customer, []);

        $invoice->markAsSending();

        $this->assertEquals(StatusEnum::Sending, $invoice->getStatus());
    }

    public function test_mark_as_sending_with_invalid_status(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Invoice must be in draft status to be sent.');

        $customer = new Customer('John Doe', 'john.doe@example.com', '12345');
        $invoice = new Invoice('123', StatusEnum::SentToClient, $customer, []);

        $invoice->markAsSending();
    }

    public function test_mark_as_sent_to_client(): void
    {
        $customer = new Customer('John Doe', 'john.doe@example.com', '12345');
        $invoice = new Invoice('123', StatusEnum::Sending, $customer, []);

        $invoice->markAsSentToClient();

        $this->assertEquals(StatusEnum::SentToClient, $invoice->getStatus());
    }

    public function test_mark_as_sent_to_client_with_invalid_status(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Invoice must be in sending status to be marked as sent-to-client.');

        $customer = new Customer('John Doe', 'john.doe@example.com', '12345');
        $invoice = new Invoice('123', StatusEnum::Draft, $customer, []);

        $invoice->markAsSentToClient();
    }
}
