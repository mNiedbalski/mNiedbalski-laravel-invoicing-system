<?php

namespace Modules\Invoices\Infrastructure\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Modules\Invoices\Application\Listeners\UpdateInvoiceStatusListener;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;

class InvoiceEventServiceProvider extends EventServiceProvider
{
    protected $listen = [
        ResourceDeliveredEvent::class => [
            UpdateInvoiceStatusListener::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
