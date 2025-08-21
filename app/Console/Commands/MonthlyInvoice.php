<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\UserInvoice;

use App\Services\InvoiceService;
use Exception;
use PDF;

class MonthlyInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voice:monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monthly Invoice';

    protected $service;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(InvoiceService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $users = $this->service->getAllUser();
            $admins = $this->service->getAllAdmin();
            $emailAdmins = $admins->pluck('email')->toArray();

            $lastMonth = strtotime('first day of last month');
            $month = date('m', $lastMonth);
            $year = date('Y', $lastMonth);

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
                        Mail::send([], [], function ($message) use ($publicFileName, $user, $year, $month) {
                            $message->to($user->email)
                                ->subject('Monthly Invoice ' . $year . '-' . $month)
                                ->attach($publicFileName);
                        });

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

            Log::info("Cron is working fine!");
            $this->info('Successfully sent month voice to everyone.');
        } catch(Exception $e) {
            Log::error($e);
            $this->error('Error sent month voice to everyone.');
        }
    }
}
