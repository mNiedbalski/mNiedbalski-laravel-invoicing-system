<?php

namespace Modules\Invoices\Application\Services;

use Modules\Invoices\Domain\Entities\Customer;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\ProductLine;
use Modules\Invoices\Domain\ValueObjects\Money;
use Modules\Invoices\Infrastructure\Adapters\InvoiceAdapter;

readonly class InvoiceService
{
    public function __construct(
        private InvoiceAdapter             $invoiceAdapter,
        private InvoiceNotificationService $invoiceNotificationService,
    ) {}

    public function createInvoice(): string
    {
        // Using pre-defined customer and product lines for the sake of simplicity (normally, these would be passed from the form)
        // We are using pre-generated UUIDs, because we assume that Customers were already in the system. If they were not, we wouldn't pass UUID here, but e-mail would have to be unique!
        $customer = new Customer('Michal Niedbalski', 'niedbalsky@gmail.com', '68a39cca-1825-430e-9920-030731213194');

        $productLine1 = new ProductLine(name: 'Running shoes', quantity: 1, unitPrice: new Money(19900));
        $productLine2 = new ProductLine(name: 'Water bottle', quantity: 3, unitPrice: new Money(200));
        $productLine3 = new ProductLine(name: 'Shirt with number tag', quantity:  1, unitPrice: new Money(5000));

        $invoice = new Invoice(customer: $customer, productLines: [$productLine1, $productLine2, $productLine3]);

        $this->invoiceAdapter->create($invoice);
        return $invoice->getId();
    }

    public function sendInvoice(string $invoiceId): void
    {
        $invoice = $this->invoiceAdapter->findById($invoiceId);

        if (!$invoice) {
            throw new \DomainException('Invoice not found');
        }

        // Validating inside the domain layer
        $invoice->markAsSending();

        $this->invoiceAdapter->update($invoice);
        $this->invoiceNotificationService->execute($invoice);
    }
    public function mockDatabase(): void
    {
        // We can assume that Customers will be all available with IDs and creating a new Customer would be done in earlier parts of the application
        // We are using pre-generated UUIDs, because we assume that Customers were already in the system. If they were not, we wouldn't pass UUID here, but e-mail would have to be unique!

        $customer = new Customer('Paul Muadib Atreides', 'duke@arrakis.com', '7651dcd0-50bf-4535-8043-322cfd8967a5');
        $productLine1 = new ProductLine(name: 'Ornithopter / Wings', quantity: 8, unitPrice: new Money(89999));
        $productLine2 = new ProductLine(name: 'Ornithopter / Back landing gear ', quantity: 2,  unitPrice: new Money(500000));
        $productLine3 = new ProductLine(name: 'Ornithopter / Front glass', quantity: 1, unitPrice: new Money(900000));
        $productLine4 = new ProductLine(name: 'Ornithopter / Assembly', quantity: 1, unitPrice: new Money(500000));
        $productLine5 = new ProductLine(name: 'Ornithopter / Electronics', quantity: 1, unitPrice: new Money(200000));
        $invoice = new Invoice(customer: $customer, productLines: [$productLine1, $productLine2, $productLine3, $productLine4, $productLine5]);
        $this->invoiceAdapter->create($invoice);

        $customer = new Customer('Darth Sidious', 'imperator@galaxy.com', '40cc0b66-b54f-4394-8d19-96e812c15350');
        $productLine1 = new ProductLine(name: 'Lightsaber', quantity: 2, unitPrice: new Money(1000000));
        $productLine2 = new ProductLine(name: 'Darth Vader - full armor kit', quantity: 1,  unitPrice: new Money(999999));
        $productLine3 = new ProductLine(name: 'Assembly', quantity: 1, unitPrice: new Money(500000));
        $productLine4 = new ProductLine(name: 'Doctors', quantity: 1, unitPrice: new Money(199900));
        $invoice = new Invoice(customer: $customer, productLines: [$productLine1, $productLine2, $productLine3, $productLine4]);
        $this->invoiceAdapter->create($invoice);

        $customer = new Customer('Forgot To Add Products', 'cant@remember.io', '5dfe979a-f18a-4366-b5d6-55c5a79d67b7');
        $invoice = new Invoice(customer: $customer);

        $this->invoiceAdapter->create($invoice);
    }
}
