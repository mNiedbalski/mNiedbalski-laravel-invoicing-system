<?php

declare(strict_types=1);

namespace Modules\Invoices\Application\Services;

use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Notifications\Api\Dtos\NotifyData;
use Modules\Notifications\Application\Facades\NotificationFacade;
use Ramsey\Uuid\Uuid;

final class InvoiceNotificationService
{
    public function execute(Invoice $invoice): void
    {
        $customer = $invoice->getCustomer();

        $notifyData = new NotifyData(
            resourceId: Uuid::fromString($invoice->getId()),
            toEmail: $customer->getEmail(),
            subject: 'Invoice Details - ' . $invoice->getId(),
            message: $this->buildMessage($invoice),
        );
        dd($notifyData);
        $notificationFacade = app(NotificationFacade::class);
        $notificationFacade->notify($notifyData);
    }

    private function buildMessage(Invoice $invoice): string
    {
        $message = "Dear " . $invoice->getCustomer()->getName() . ",\n\n";
        $message .= "Here are the details of your invoice:\n";
        $message .= "Invoice ID: " . $invoice->getId() . "\n";
        $message .= "Status: " . $invoice->getStatus()->value . "\n";
        $message .= "Total Price: " . number_format($invoice->getTotalPrice()->getAmount() / 100, 2) . " " . $invoice->getTotalPrice()->getCurrency() . "\n\n";
        $message .= "Thank you for your business.\n";

        return $message;
    }
}
