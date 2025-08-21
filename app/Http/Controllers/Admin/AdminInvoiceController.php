<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SendInvoiceRequest;
use App\Models\UserInvoice;
use App\Services\InvoiceService;
use App\Services\Staff\StaffInvoiceService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use PDF;

class AdminInvoiceController extends AdminBaseController
{
    protected $invoiceService;

    public function __construct(StaffInvoiceService $staffInvoiceService, InvoiceService $service)
    {
        parent::__construct();

        $this->staffInvoiceService = $staffInvoiceService;
        $this->service = $service;
    }

    /**
     * View for listing invoice
     *
     * @return \Illuminate\View\View
     */
    public function list(Request $request)
    {
        try {
            $input = $request->only('email', 'month', 'year');
            $info = $this->staffInvoiceService->list($input);

            return view('admin.invoice.list', $info);
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Display a detail of invoice.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function detail(Request $request, $id)
    {
        $invoice = $this->staffInvoiceService->detail($id);

        return view('admin.invoice.detail', $invoice);
    }

    /**
     * Send invoice to all user and notify admin.
     *
     * @param SendInvoiceRequest $request
     * @return \Illuminate\Contracts\View\View
     */
    public function send(SendInvoiceRequest $request)
    {
        try {
            $users = $this->service->getAllUser();
            $admins = $this->service->getAllAdmin();
            $emailAdmins = $admins->pluck('email')->toArray();

            $month = $request->target_month;
            $year = $request->target_year;

            $successUsers = [];
            $failUsers = [];

            foreach($users as $user) {
                $value = $this->service->showProfile($user->id, $month, $year);
                if ($value != null) {
                    try {
                        //Save file
                        $path = UserInvoice::IMG_FOLDER . DIRECTORY_SEPARATOR . $user->id;
                        $publicPath = public_path('imgs') . DIRECTORY_SEPARATOR . $path;
                        File::ensureDirectoryExists($publicPath);

                        $fileName = $year . '_' . $month . '_' . cleanName('invoice.pdf');
                        $publicFileName = $publicPath . DIRECTORY_SEPARATOR . $fileName;

                        $pdf = PDF::loadView('invoice', $value);
                        $pdf->save($publicFileName);

                        // Save info
                        UserInvoice::create([
                            'user_id' => $user->id,
                            'month' =>  $month,
                            'year' => $year,
                            'file' => 'imgs' . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $fileName,
                            'inbound' => $value['totalPriceInbound'],
                            'outbound' => $value['totalPriceOutbound'],
                            'relabel' => $value['totalPriceRelabel'],
                            'repack' => $value['totalPriceRepack'],
                            'removal' => $value['totalPriceRemoval'],
                            'return' => $value['totalPriceReturn'],
                            'storage' => $value['totalPriceStored'],
                            'warehouse_labor'  => $value['totalPriceLabor'],
                            'tax' => $value['tax'],
                            'balance' => $value['balance'],
                        ]);

                        //Attach to mail
                        // Mail::send([], [], function ($message) use ($publicFileName, $user, $year, $month) {
                        //     $message->to($user->email)
                        //         ->subject('Monthly Invoice ' . $year . '-' . $month)
                        //         ->attach($publicFileName);
                        // });

                        // Success User
                        $successUsers[] = $user->email;
                    } catch(Exception $e) {
                        Log::error($e);

                        // Fail User
                        $failUsers[] = $user->email;
                    }
                } else {
                    // Fail User
                    $failUsers[] = $user->email;
                }
            }

            // Email admin to notify about successful or failed cases

            $userData = [
                'targetMonth' => $month,
                'targetYear' => $year,
                'success' => $successUsers,
                'fail' => $failUsers
            ];

            Mail::send('admin_invoice', $userData, function($message) use ($emailAdmins, $year, $month)
            {
                $message->to($emailAdmins)
                    ->subject('Admin Email Monthly Invoice ' . $year . '-' . $month);
            });

            return redirect()->route('admin.invoice.list')->with('success', "Send Invoice successfully.");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->route('admin.invoice.list')->with('success', "Send Invoice fail.");
        }
    }
}
