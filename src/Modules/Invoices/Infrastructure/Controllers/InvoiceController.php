<?php

namespace Modules\Invoices\Infrastructure\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Invoices\Application\Services\SendInvoiceNotification;
use Modules\Invoices\Domain\Entities\Customer;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\ProductLine;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\ValueObjects\IdService;
use Modules\Invoices\Domain\ValueObjects\Money;
use Modules\Invoices\Infrastructure\Adapters\InvoiceAdapter;
use Modules\Invoices\Infrastructure\Models\InvoiceModel;
use Modules\Notifications\Application\Facades\NotificationFacade;

class InvoiceController
{
    public function __construct(
        private readonly InvoiceAdapter $invoiceAdapter
    )
    {
    }

    public function showTaskPage()
    {
        $invoices = InvoiceModel::all();
        return view('task-page', ['invoices' => $invoices]);
    }
    public function createInvoice(Request $request)
    {
        $customer = new Customer('Michal Niedbalski', 'niedbalsky@gmail.com');
        $productLine1 = new ProductLine('Running shoes', 1, new Money(19900));
        $productLine2 = new ProductLine('Water bottle', 3, new Money(200));
        $productLine3 = new ProductLine('Shirt with number tag', 1, new Money(5000));

        $invoice = new Invoice(
            IdService::generate(),
            StatusEnum::Draft,
            $customer,
            [$productLine1, $productLine2, $productLine3]
        );

        // Saving invoice in InMemoryInvoiceRepository. Normally, this would be saved in database,
        // but given that database connection wasn't mentioned in the task, we are using in-memory storage.

        $this->invoiceAdapter->persist($invoice);

        session()->flash('success', 'Invoice ' . $invoice->getId() . ' created successfully!');
        return redirect()->back();
    }
    public function viewInvoice(Request $request): View
    {
        $id = $request->query('id'); // Retrieve 'id' from query parameters
        $invoice = $this->invoiceAdapter->fromId($id); // Fetch the invoice by ID

        // Check if the invoice exists (for example if we want to add input searching in the future instead of id-mapped buttons)
        if (!$invoice) {
            return view('invoices.view', [
                'error' => 'Invoice not found'
            ]);
        }
        // If the invoice exists, return the view with the invoice data
        return view('invoices.view', ['invoice' => $invoice]);
    }

    public function sendInvoice(Request $request)
    {
        $id = $request->input('id');
        $invoice = $this->invoiceAdapter->fromId($id);

        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        // Validate invoice status and product lines
        if ($invoice->getStatus() !== StatusEnum::Draft) {
            return response()->json(['error' => 'Invoice must be in draft status to be sent'], 400);
        }

        foreach ($invoice->getProductLines() as $productLine) {
            if ($productLine->getQuantity() <= 0 || $productLine->getUnitPrice()->getAmount() <= 0) {
                return response()->json(['error' => 'Product lines must have positive quantity and unit price'], 400);
            }
        }

        // Send notification using SendInvoiceNotification service (I've created my own service to encapsulate the logic of sending invoice notifications)
        $sendInvoiceNotification = app(SendInvoiceNotification::class);
        $sendInvoiceNotification->execute($invoice);

        $invoice->setStatus(StatusEnum::Sending);
        $this->invoiceAdapter->persist($invoice);

        return response()->json(['message' => 'Invoice is being sent'], 200);
    }

}
