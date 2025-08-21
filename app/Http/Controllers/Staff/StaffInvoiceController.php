<?php

namespace App\Http\Controllers\Staff;

use App\Services\Staff\StaffInvoiceService;
use Illuminate\Http\Request;

class StaffInvoiceController extends StaffBaseController
{
    protected $invoiceService;

    public function __construct(StaffInvoiceService $invoiceService)
    {
        parent::__construct();

        $this->invoiceService = $invoiceService;
    }

    /**
     * View for listing invoice
     *
     * @return \Illuminate\View\View
     */
    public function list(Request $request)
    {
        $input = $request->only('email', 'month', 'year');
        $info = $this->invoiceService->list($input);

        return view('staff.invoice.list', $info);
    }

    /**
     * Display a detail of invoice.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function detail(Request $request, $id)
    {
        $invoice = $this->invoiceService->detail($id);

        return view('staff.invoice.detail', $invoice);
    }
}
