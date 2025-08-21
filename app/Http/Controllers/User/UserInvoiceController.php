<?php

namespace App\Http\Controllers\User;

use App\Services\User\UserInvoiceService;
use Illuminate\Http\Request;

class UserInvoiceController extends UserBaseController
{
    protected $invoiceService;

    public function __construct(UserInvoiceService $invoiceService)
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
        $input = $request->only('month', 'year');
        $info = $this->invoiceService->list($input);

        return view('user.invoice.list', $info);
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

        return view('user.invoice.detail', $invoice);
    }
}
