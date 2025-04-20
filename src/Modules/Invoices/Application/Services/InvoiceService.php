<?php

namespace Modules\Invoices\Application\Services;

use Modules\Invoices\Domain\Entities\Customer;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\ProductLine;
use Modules\Invoices\Domain\ValueObjects\Money;
use Modules\Invoices\Infrastructure\Adapters\InvoiceAdapter;
use Modules\Notifications\Application\Facades\NotificationFacade;
use Modules\Notifications\Api\Dtos\NotifyData;
use Ramsey\Uuid\Uuid;

class InvoiceService
{
    public function __construct(
        private readonly InvoiceAdapter $invoiceAdapter,
        private readonly InvoiceNotificationService $invoiceNotificationService,
    ) {}

    public function createInvoice(): string
    {
        // Using pre-defined customer and product lines for the sake of simplicity (normally, these would be passed from the form)

        $customer = new Customer('Michal Niedbalski', 'niedbalsky@gmail.com', '68a39cca-1825-430e-9920-030731213194');

        $productLine1 = new ProductLine(name: 'Running shoes', quantity: 1, unitPrice: new Money(19900));
        $productLine2 = new ProductLine(name: 'Water bottle', quantity: 3, unitPrice: new Money(200));
        $productLine3 = new ProductLine(name: 'Shirt with number tag', quantity:  1, unitPrice: new Money(5000));

        $invoice = new Invoice(customer: $customer, productLines: [$productLine1, $productLine2, $productLine3]);

        // Saving invoice in InMemoryInvoiceRepository. Normally, this would be saved in database,
        // but given that database connection wasn't mentioned in the task, we are using in-memory storage.

        $this->invoiceAdapter->createModelAndPersist($invoice);
        return $invoice->getId();
    }

    public function sendInvoice(string $invoiceId): void
    {
        $invoice = $this->invoiceAdapter->fromId($invoiceId);

        if (!$invoice) {
            throw new \DomainException('Invoice not found');
        }

        // Checking product lines (quantities and prices are already validated in ProductLine constructor)
        if (empty($invoice->getProductLines())) {
            throw new \DomainException('Invoice must have at least one product line');
        }

        // If I misunderstood the conception of ProductLines in the Invoices, meaning that productLines are mutable then the following line should be uncommented
//        $invoice->validateProductLines();
        $invoice->markAsSending();
        $this->invoiceAdapter->updateAndPersist($invoice);
        $this->invoiceNotificationService->execute($invoice);
    }
    public function mockDatabase(): void
    {
        $customer = new Customer('Paul Muadib Atreides', 'duke@arrakis.com');
        $productLine1 = new ProductLine(name: 'Ornithopter / Wings', quantity: 8, unitPrice: new Money(89999));
        $productLine2 = new ProductLine(name: 'Ornithopter / Back landing gear ', quantity: 2,  unitPrice: new Money(500000));
        $productLine3 = new ProductLine(name: 'Ornithopter / Front glass', quantity: 1, unitPrice: new Money(900000));
        $productLine4 = new ProductLine(name: 'Assembly', quantity: 1, unitPrice: new Money(500000));
        $productLine5 = new ProductLine(name: 'Ornithopter / Electronics', quantity: 1, unitPrice: new Money(200000));
        $invoice = new Invoice(customer: $customer, productLines: [$productLine1, $productLine2, $productLine3, $productLine4, $productLine5]);
        $this->invoiceAdapter->createModelAndPersist($invoice);

        $customer = new Customer('Darth Sidious', 'imperator@galaxy.com');
        $productLine1 = new ProductLine(name: 'Lightsaber', quantity: 2, unitPrice: new Money(1000000));
        $productLine2 = new ProductLine(name: 'Darth Vader - full armor kit', quantity: 1,  unitPrice: new Money(999999));
        $productLine3 = new ProductLine(name: 'Assembly', quantity: 1, unitPrice: new Money(500000));
        $productLine4 = new ProductLine(name: 'Doctors', quantity: 1, unitPrice: new Money(199900));
        $invoice = new Invoice(customer: $customer, productLines: [$productLine1, $productLine2, $productLine3, $productLine4]);
        $this->invoiceAdapter->createModelAndPersist($invoice);

        $customer = new Customer('Forgot To Add Products', 'cant@remember.io');
        $invoice = new Invoice(customer: $customer);

        $this->invoiceAdapter->createModelAndPersist($invoice);
    }
}
