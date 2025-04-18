<?php

namespace Modules\Invoices\Infrastructure\Controllers;

use Modules\Invoices\Domain\Entities\Customer;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\ValueObjects\IdService;
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
        // Validate input
        $data = json_decode($request->getContent(), true);
        if (empty($data['customerName']) || empty($data['customerEmail'])) {
            return new JsonResponse(['error' => 'Customer name and email are required.'], 400);
        }

        $customer = new Customer($data['customerName'], $data['customerEmail']);
        $invoice = new Invoice(
            IdService::generate(),
            StatusEnum::Draft,
            $customer
        );

        $this->repository->save($invoice);

        return new JsonResponse([
            'id' => $invoice->getId(),
            'status' => $invoice->getStatus()->value,
            'customer' => [
                'name' => $customer->getName(),
                'email' => $customer->getEmail(),
            ],
            'totalPrice' => $invoice->getTotalPrice()->getAmount(),
        ], 201);
    }
}
