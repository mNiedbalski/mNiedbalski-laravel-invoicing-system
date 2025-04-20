<?php

namespace Modules\Invoices\Infrastructure\Adapters;

use Modules\Invoices\Domain\Entities\Customer;
use Modules\Invoices\Domain\Entities\Invoice as InvoiceEntity;
use Modules\Invoices\Domain\Entities\ProductLine;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\ValueObjects\Money;
use Modules\Invoices\Infrastructure\Models\CustomerModel;
use Modules\Invoices\Infrastructure\Models\InvoiceModel;
use Modules\Invoices\Infrastructure\Models\ProductLineModel;

class InvoiceAdapter
{
    public function createModelAndPersist(InvoiceEntity $invoiceEntity): InvoiceModel
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

        return $invoiceModel;
    }
    public function fromId(string $id): ?InvoiceEntity
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
            $productLines,
            $invoiceModel->id,
            StatusEnum::from($invoiceModel->status),
        );
    }

    /**
     * This method only updates the status of the invoice in database, because Invoice data like customer, product lines and prices are immutable.
     * @param InvoiceEntity $invoiceEntity
     * @return void
     */
    public function updateAndPersist(InvoiceEntity $invoiceEntity): void
    {
        $invoiceModel = InvoiceModel::updateOrCreate(
            ['id' => $invoiceEntity->getId()],
            [
                'status' => $invoiceEntity->getStatus()->value,
            ]
        );
    }
}
