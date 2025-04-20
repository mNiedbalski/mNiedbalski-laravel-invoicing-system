<?php

namespace Modules\Invoices\Infrastructure\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Modules\Invoices\Application\Services\InvoiceNotificationService;
use Modules\Invoices\Domain\Entities\Customer;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\ProductLine;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\ValueObjects\IdService;
use Modules\Invoices\Domain\ValueObjects\Money;
use Modules\Invoices\Infrastructure\Adapters\InvoiceAdapter;
use Modules\Invoices\Infrastructure\Models\CustomerModel;
use Modules\Invoices\Infrastructure\Models\InvoiceModel;
use Modules\Notifications\Application\Facades\NotificationFacade;

class InvoiceController
{
    public function __construct(
        private readonly InvoiceAdapter             $invoiceAdapter,
        private readonly InvoiceNotificationService $invoiceNotificationService,
    )
    {
    }

    public function showTaskPage()
    {
        $invoices = InvoiceModel::all();
        return view('task-page', ['invoices' => $invoices]);
    }

    public function createInvoice(Request $request): RedirectResponse
    {
        try {
            // Using pre-defined customer and product lines for the sake of simplicity (normally, these would be passed from the form)
            $customer = new Customer('Michal Niedbalski', 'niedbalsky@gmail.com', '68a39cca-1825-430e-9920-030731213194');
            $productLine1 = new ProductLine('Running shoes', 1, new Money(19900));
            $productLine2 = new ProductLine('Water bottle', 3, new Money(-200));
            $productLine3 = new ProductLine('Shirt with number tag', 1, new Money(5000));

            $invoice = new Invoice(
                IdService::generate(),
                StatusEnum::Draft,
                $customer,
                []
            );

            // Saving invoice in InMemoryInvoiceRepository. Normally, this would be saved in database,
            // but given that database connection wasn't mentioned in the task, we are using in-memory storage.

            $this->invoiceAdapter->createModelAndPersist($invoice);

            session()->flash('success', 'Invoice ' . $invoice->getId() . ' created successfully!');
            return redirect()->back();

        } catch (\InvalidArgumentException $e) {
            // Constructor validation errors (e.g. invalid email format, not positive quantity, unit price)
            session()->flash('error', 'Validation error: ' . $e->getMessage());
            return redirect()->back();

        } catch (\Exception $e) {
            // Other exceptions (e.g. database errors)
            logger()->error('Invoice creation failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Failed to create invoice. Please try again.');
            return redirect()->back();
        }

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
        try {
            $id = $request->input('id');
            $invoice = $this->invoiceAdapter->fromId($id);

            if (!$invoice) {
                session()->flash('error', 'Invoice not found');
                return redirect()->back();
            }

            // Checking product lines (quantities and prices are already validated in ProductLine constructor)
            if (empty($invoice->getProductLines())) {
                session()->flash('error', 'Invoice must have at least one product line');
                return redirect()->back();
            }

            // Setter has guardian that validates the status
            $invoice->setStatus(StatusEnum::Sending);
            $this->invoiceAdapter->updateAndPersist($invoice);
            $this->invoiceNotificationService->execute($invoice);

            session()->flash('success', 'Invoice ' . $invoice->getId() . ' is being sent!');
            return redirect()->back();

        } catch (\InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->back();
        } catch (\Exception $e) {
            session()->flash('error', 'An unexpected error occurred while sending the invoice');
            logger()->error('Invoice sending failed: ' . $e->getMessage());
            return redirect()->back();
        }
    }

}
