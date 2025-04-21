<?php

namespace Modules\Invoices\Presentation\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Invoices\Application\Services\InvoiceService;
use Modules\Invoices\Infrastructure\Adapters\InvoiceAdapter;
use Modules\Invoices\Infrastructure\Models\InvoiceModel;

/**
 *  Class responsible for handling invoice-related requests.
 * This class uses the InvoiceAdapter to load invoice from database and the business logic operations are performed in InvoiceService to ensure that DDD principles are followed.
 */
readonly class InvoiceController
{
    public function __construct(
        private InvoiceAdapter             $invoiceAdapter,
        private InvoiceService             $invoiceService,
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
            // Here we would use passed data from the form
            $invoiceId = $this->invoiceService->createInvoice();

            session()->flash('success', 'Invoice ' . $invoiceId . ' created successfully!');
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
        try {
            $id = $request->query('id'); // Retrieve 'id' from query parameters
            $invoice = $this->invoiceAdapter->findById($id); // Fetch the invoice by ID

            // If the invoice exists, return the view with the invoice data
            return view('invoices.view', ['invoice' => $invoice]);
        } catch (\DomainException $e) {
            return view('invoices.view', [
                'error' => 'Invoice not found'
            ]);
        }
    }

    public function sendInvoice(Request $request): RedirectResponse
    {
        try {
            $id = $request->input('id');

            $this->invoiceService->sendInvoice($id);

            session()->flash('success', 'Invoice ' . $id . ' is being sent!');
            return redirect()->back();

        } catch (\InvalidArgumentException | \DomainException $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->back();
        } catch (\Exception $e) {
            session()->flash('error', 'An unexpected error occurred while sending the invoice');
            logger()->error('Invoice sending failed: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Bonus for testing purposes
    public function mockDatabase(): RedirectResponse
    {
        try {

        $this->invoiceService->mockDatabase();
        session()->flash('success', 'Mock invoices created successfully!');

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

}
