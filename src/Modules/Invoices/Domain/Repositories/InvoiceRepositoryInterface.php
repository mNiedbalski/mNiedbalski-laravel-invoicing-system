<?php

namespace Modules\Invoices\Domain\Repositories;


use Modules\Invoices\Domain\Entities\Invoice;

/**
 * Interface InvoiceRepositoryInterface
 * Repository interface created to adhere to DDD principles.
 */
interface InvoiceRepositoryInterface
{
    public function create(Invoice $invoice): void;

    public function findById(string $id): ?Invoice;

    public function update(Invoice $invoice): void;
}
