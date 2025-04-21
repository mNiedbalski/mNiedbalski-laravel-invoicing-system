<?php
//declare(strict_types=1);
//
//namespace Tests\Feature\Invoices\Application\Listeners;
//
//use Illuminate\Support\Facades\Event;
//use Illuminate\Support\Facades\Log;
//use Modules\Invoices\Application\Listeners\UpdateInvoiceStatusListener;
//use Modules\Invoices\Domain\Entities\Customer;
//use Modules\Invoices\Domain\Entities\ProductLine;
//use Modules\Invoices\Domain\ValueObjects\IdService;
//use Modules\Invoices\Domain\ValueObjects\Money;
//use Modules\Invoices\Infrastructure\Adapters\InvoiceAdapter;
//use Modules\Notifications\Api\Events\ResourceDeliveredEvent;
//use Modules\Invoices\Domain\Entities\Invoice;
//use Modules\Invoices\Domain\Enums\StatusEnum;
//use Ramsey\Uuid\Uuid;
//use Tests\TestCase;
//
//final class UpdateInvoiceStatusListenerTest extends TestCase
//{
//    private InvoiceAdapter $invoiceAdapter;
//    protected function setUp(): void
//    {
//        parent::setUp();
//        $this->invoiceAdapter = $this->app->make(InvoiceAdapter::class);
//    }
//
//    public function testListenerIsTriggered(): void
//    {
//
//        $invoiceAdapterMock = $this->createMock(InvoiceAdapter::class);
//
//        $customer = new Customer('John Doe', 'john.doe@example.com');
//        $productLine1 = new ProductLine(name: 'Running shoes', quantity: 1, unitPrice: new Money(19900));
//        $productLine2 = new ProductLine(name: 'Water bottle', quantity: 3, unitPrice: new Money(200));
//        $invoice = new Invoice($customer, StatusEnum::Sending, [$productLine1, $productLine2], IdService::generate()); // If ID is not assigned to Invoice, status will be set to Draft by default (Explained in Invoice Entity class)
//
//        $this->invoiceAdapter->create($invoice);
//
//        Log::shouldReceive('info')->once()->with('Triggered UpdateInvoiceStatusListener');
//        Log::shouldReceive('error')->zeroOrMoreTimes(); // Add expectation for error logging
//
//        // Trigger the event
//        $event = new ResourceDeliveredEvent(Uuid::fromString($invoice->getId()));
//        Event::dispatch($event);
//
//        // Assert that the listener was triggered
//        $listener = new UpdateInvoiceStatusListener($this->invoiceAdapter);
//        $listener->handle($event);
//
//        // Assert that status was changed
//        $this->assertEquals(StatusEnum::SentToClient, $invoice->getStatus());
//    }
//}
