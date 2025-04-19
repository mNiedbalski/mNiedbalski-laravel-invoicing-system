<?php

namespace Modules\Invoices\Infrastructure\Adapters;

use Modules\Invoices\Domain\Entities\Invoice as InvoiceEntity;
use Modules\Invoices\Infrastructure\Models\CustomerModel;
use Modules\Invoices\Infrastructure\Models\InvoiceModel;
use Modules\Invoices\Infrastructure\Models\ProductLineModel;

class InvoiceAdapter
{
    public static function toModel(InvoiceEntity $invoiceEntity): InvoiceModel
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
}
