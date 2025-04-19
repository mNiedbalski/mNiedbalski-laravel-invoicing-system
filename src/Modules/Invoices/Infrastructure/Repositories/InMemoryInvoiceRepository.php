<?php

namespace Modules\Invoices\Infrastructure\Repositories;

use Modules\Invoices\Domain\Entities\Invoice;

class InMemoryInvoiceRepository
{
    private array $invoices = [];

    public function save(Invoice $invoice): void
    {
        $this->invoices[$invoice->getId()] = $invoice;
    }

    public function findById(string $id): ?Invoice
    {
        return $this->invoices[$id] ?? null;
    }

    public function all(): array
    {
        return $this->invoices;
    }
}
