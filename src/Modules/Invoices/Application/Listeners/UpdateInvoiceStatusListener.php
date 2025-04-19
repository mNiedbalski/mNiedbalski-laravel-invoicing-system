<?php

namespace Modules\Invoices\Application\Listeners;

use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Infrastructure\Adapters\InvoiceAdapter;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;

class UpdateInvoiceStatusListener
{
    public function __construct(private InvoiceAdapter $invoiceAdapter)
    {
    }

    public function handle(ResourceDeliveredEvent $event): void
    {
        $invoice = $this->invoiceAdapter->fromId($event->resourceId);

        if ($invoice && $invoice->getStatus() === StatusEnum::Sending) {
            $invoice->setStatus(StatusEnum::SentToClient);
            $this->invoiceAdapter->persist($invoice);
        }
    }
}
