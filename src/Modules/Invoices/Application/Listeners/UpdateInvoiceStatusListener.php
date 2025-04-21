<?php

namespace Modules\Invoices\Application\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Infrastructure\Adapters\InvoiceAdapter;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;

/**
 * @event \Modules\Notifications\Api\Events\ResourceDeliveredEvent
 */
class UpdateInvoiceStatusListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private readonly InvoiceAdapter $invoiceAdapter
    )
    {
    }

    public function handle(ResourceDeliveredEvent $event): void
    {
        try {
            Log::info('Triggered UpdateInvoiceStatusListener');
            $invoice = $this->invoiceAdapter->findById($event->resourceId);
            $invoice->markAsSentToClient();
            $this->invoiceAdapter->update($invoice);

        } catch (\DomainException $e) {
            Log::error('Error updating invoice status: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Unexpected error occurred!: ' . $e->getMessage());
        }
    }
}
