<?php

namespace Modules\Invoices\Infrastructure\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Invoices\Domain\Entities\Customer;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\ProductLine;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\ValueObjects\IdService;
use Modules\Invoices\Domain\ValueObjects\Money;
use Modules\Invoices\Infrastructure\Adapters\InvoiceAdapter;
use Modules\Invoices\Infrastructure\Models\InvoiceModel;

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

        $this->invoiceAdapter->toModel($invoice);

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
}
