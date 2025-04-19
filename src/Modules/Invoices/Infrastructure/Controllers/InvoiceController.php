<?php

namespace Modules\Invoices\Infrastructure\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Invoices\Domain\Entities\Customer;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\ProductLine;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\ValueObjects\IdService;
use Modules\Invoices\Domain\ValueObjects\Money;
use Modules\Invoices\Infrastructure\Repositories\InMemoryInvoiceRepository;

class InvoiceController
{
    private InMemoryInvoiceRepository $repository;

    public function __construct(InMemoryInvoiceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createInvoice(Request $request)
    {
        $customer = new Customer('Maximus Decimus Meridius', 'commander@north.com');
        $productLine1 = new ProductLine('Product 1', 3, new Money(100));
        $productLine2 = new ProductLine('Product 2', 2, new Money(200));
        $productLine3 = new ProductLine('Product 3', 1, new Money(300));

        $invoice = new Invoice(
            IdService::generate(),
            StatusEnum::Draft,
            $customer,
            [$productLine1, $productLine2, $productLine3]
        );

        // Saving invoice in InMemoryInvoiceRepository. Normally, this would be saved in database,
        // but given that database connection wasn't mentioned in the task, we are using in-memory storage.
        $this->repository->save($invoice);

        session()->flash('success', 'Invoice ' . $invoice->getId() . ' created successfully!');
        return redirect()->back();
    }
    public function viewInvoice(Request $request): JsonResponse
    {
        $id = $request->query('id'); // Retrieve 'id' from query parameters
        $invoice = $this->repository->findById($id);

        if (!$invoice) {
            return new JsonResponse(['error' => 'Invoice not found'], 404);
        }

        $invoiceData = [
            'Invoice ID' => $invoice->getId(),
            'Invoice Status' => $invoice->getStatus()->value,
            'Customer Name' => $invoice->getCustomer()->getName(),
            'Customer Email' => $invoice->getCustomer()->getEmail(),
            'Invoice Product Lines' => array_map(function (ProductLine $productLine) {
                return [
                    'Product Name' => $productLine->getName(),
                    'Quantity' => $productLine->getQuantity(),
                    'Unit Price' => $productLine->getUnitPrice()->getAmount(),
                    'Total Unit Price' => $productLine->getTotalUnitPrice()->getAmount(),
                ];
            }, $invoice->getProductLines()),
            'Total Price' => $invoice->getTotalPrice()->getAmount(),
        ];

        return new JsonResponse($invoiceData);
    }
}
