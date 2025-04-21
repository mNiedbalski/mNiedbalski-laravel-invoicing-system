<?php

namespace Modules\Invoices\Infrastructure\Adapters;

use Modules\Invoices\Domain\Entities\Customer;
use Modules\Invoices\Domain\Entities\Invoice as InvoiceEntity;
use Modules\Invoices\Domain\Entities\ProductLine;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Infrastructure\Models\CustomerModel;
use Modules\Invoices\Infrastructure\Models\InvoiceModel;
use Modules\Invoices\Infrastructure\Models\ProductLineModel;

class InvoiceAdapter implements InvoiceRepositoryInterface
{
    public function create(InvoiceEntity $invoice): void
    {
        $this->createModelAndPersist($invoice);
    }

    public function update(InvoiceEntity $invoice): void
    {
        $this->updateAndPersist($invoice);
    }

    /** Creating corresponding ORM Eloquent models and persisting them to the database.
     * @param InvoiceEntity $invoiceEntity
     * @return void
     */
    private function createModelAndPersist(InvoiceEntity $invoiceEntity): void
    {
        // Update or create customer
        $customer = $invoiceEntity->getCustomer();
        $customerModel = CustomerModel::updateOrCreate(
            ['id' => $customer->getId()],
            [
                'name' => $customer->getName(),
                'email' => $customer->getEmail()
            ]
        );

        // Update or create invoice
        $invoiceModel = InvoiceModel::updateOrCreate(
            ['id' => $invoiceEntity->getId()],
            [
                'status' => $invoiceEntity->getStatus()->value,
                'customer_id' => $customerModel->id,
                'total_price' => $invoiceEntity->getTotalPrice()
            ]
        );

        foreach ($invoiceEntity->getProductLines() as $productLine) {
            ProductLineModel::updateOrCreate(
                ['id' => $productLine->getId()],
                [
                    'name' => $productLine->getName(),
                    'quantity' => $productLine->getQuantity(),
                    'unit_price' => $productLine->getUnitPrice(),
                    'total_unit_price' => $productLine->getTotalUnitPrice(),
                    'invoice_id' => $invoiceModel->id
                ]
            );
        }

    }
    public function findById(string $id): ?InvoiceEntity
    {
        // Fetch the invoice model with related customer and product lines
        $invoiceModel = InvoiceModel::with('customer', 'productLines')->find($id);

        if (!$invoiceModel) {
            throw new \DomainException('Invoice not found');
        }

        // Hydrate the Customer entity
        $customer = new Customer(
            $invoiceModel->customer->name,
            $invoiceModel->customer->email
        );

        // Hydrate the ProductLine entities
        $productLines = $invoiceModel->productLines->map(function ($productLineModel) {
            return new ProductLine(
                $productLineModel->name,
                $productLineModel->quantity,
                $productLineModel->unit_price,
            );
        })->toArray();

        // Hydrate and return the Invoice entity
        return new InvoiceEntity(
            $customer,
            StatusEnum::from($invoiceModel->status),
            $productLines,
            $invoiceModel->id,
        );
    }

    /**
     * This method only updates the status of the invoice in database, because Invoice data like customer, product lines and prices are immutable.
     * @param InvoiceEntity $invoiceEntity
     * @return void
     */
    private function updateAndPersist(InvoiceEntity $invoiceEntity): void
    {
        InvoiceModel::updateOrCreate(
            ['id' => $invoiceEntity->getId()],
            [
                'status' => $invoiceEntity->getStatus()->value,
            ]
        );
    }
}
