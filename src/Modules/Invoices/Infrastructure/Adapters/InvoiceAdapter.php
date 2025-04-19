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
    public function persist(InvoiceEntity $invoiceEntity): InvoiceModel
    {
        //First we save customer
        $customer = $invoiceEntity->getCustomer();
        $customerModel = new CustomerModel([
            'id' => $customer->getId(),
            'name' => $customer->getName(),
            'email' => $customer->getEmail(),
        ]);
        $customerModel->save();

        //Then we save invoice
        $invoiceModel = new InvoiceModel();
        $invoiceModel->id = $invoiceEntity->getId();
        $invoiceModel->status = $invoiceEntity->getStatus()->value;
        $invoiceModel->customer_id = $invoiceEntity->getCustomer()->getId();
        $invoiceModel->total_price = $invoiceEntity->getTotalPrice();

        $invoiceModel->save();

        //And since invoice model doesn't know about product lines, we save them later ( Invoice -< Product Lines )
        foreach ($invoiceEntity->getProductLines() as $productLine) {
            $productLine = new ProductLineModel([
                'id' => $productLine->getId(),
                'name' => $productLine->getName(),
                'quantity' => $productLine->getQuantity(),
                'unit_price' => $productLine->getUnitPrice(),
                'total_unit_price' => $productLine->getTotalUnitPrice(),
                'invoice_id' => $invoiceModel->id,
            ]);
            $productLine->save();
        }

        return $invoiceModel;
    }
    public function fromId(string $id): ?InvoiceEntity
    {
        // Fetch the invoice model with related customer and product lines
        $invoiceModel = InvoiceModel::with('customer', 'productLines')->find($id);

        if (!$invoiceModel) {
            return null; // Return null if the invoice is not found
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
            $invoiceModel->id,
            StatusEnum::from($invoiceModel->status),
            $customer,
            $productLines
        );
    }
}
