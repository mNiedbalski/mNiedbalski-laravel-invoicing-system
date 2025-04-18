<?php

namespace Modules\Invoices\Infrastructure\Controllers;

use Modules\Invoices\Domain\Entities\Customer;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\ProductLine;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\ValueObjects\IdService;
use Modules\Invoices\Domain\ValueObjects\Money;
use Modules\Invoices\Infrastructure\Repositories\InMemoryInvoiceRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class InvoiceController
{
    private InMemoryInvoiceRepository $repository;

    public function __construct(InMemoryInvoiceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createInvoice(Request $request): JsonResponse
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
//        $this->repository->save($invoice);

        return new JsonResponse([
            'Invoice ID' => $invoice->getId(),
            'Invoice Status' => $invoice->getStatus()->value,
            'Customer Name' => $customer->getName(),
            'Customer Email' => $customer->getEmail(),
            'Invoice Product Lines' => array_map(function (ProductLine $productLine) {
                return [
                    'Product Name' => $productLine->getName(),
                    'Quantity' => $productLine->getQuantity(),
                    'Unit Price' => $productLine->getUnitPrice()->getAmount(),
                    'Total Unit Price' => $productLine->getTotalUnitPrice()->getAmount(),
                ];
            }, $invoice->getProductLines()),
            'Total Price' => $invoice->getTotalPrice()->getAmount(),
        ], 201);
    }
}
