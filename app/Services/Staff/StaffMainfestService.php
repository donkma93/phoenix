<?php

namespace App\Services\Staff;

use App\Models\MainfestDetail;
use App\Models\MainfestUs;
use App\Models\Order;
use App\Models\OrderJourney;
use App\Models\PackageGroup;
use App\Models\Warehouse;
use App\Services\StaffBaseServiceInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use File;

class StaffMainfestService extends StaffBaseService implements StaffBaseServiceInterface
{
 
    public function index($request)
    {
        Log::info('Mainfest sService');
        $data = MainfestUs::paginate();
        foreach ($data as $mainfest) {

            $mainfestDetail = MainfestDetail::orderBy('receive_date', 'ASC')->where('mainfest_id', $mainfest->id)->get()->toArray();
            $minReceivedDate = MainfestDetail::where('mainfest_id', $mainfest->id)->selectRaw('MIN(receive_date) AS receive_date')->get()->toArray();
            if (count($minReceivedDate) > 0) {
                $mainfest['receive_date'] = $minReceivedDate[0]['receive_date'];
            }
            $receive_count = 0;
            if (count($mainfestDetail) > 0) {
                foreach ($mainfestDetail as $item) {
                    if (isset($item['receive_date'])) {
                       $receive_count++;
                    }
                }
                $mainfest['receive_count'] = $receive_count;
            }

            if ($receive_count != 0 && $receive_count == count($mainfestDetail) && $mainfest->status == MainfestUs::NEW) {
                MainfestUs::where('id', $mainfest->id)->update(['status' => MainfestUs::DONE]);
            }

        }

        return $data;
    }

    public function getMNFDetail($mainfest_id)
    {
        $list = MainfestDetail::orderBy('created_at', 'DESC')->where('mainfest_id', $mainfest_id)->paginate();
        foreach ($list as $item) {
            $order = Order::orderBy('created_at', 'DESC')->with([
                'orderTransaction',
            ])
                ->find($item->order_id);
            $item['tracking_number'] = $order->orderTransaction->tracking_number;
        }

        return $list;
    }

    public function create()
    {
        $orderJourneys = OrderJourney::whereNotNull('id_packing_list')
            ->where('inout_type', OrderJourney::INOUT_TYPE_CREATED)
            ->where('is_mainfest_status', OrderJourney::MAINFEST_CREATED)
            ->get();

        $orders = array();
        foreach ($orderJourneys as $orderJourney) {
            $order =  $order = Order::orderBy('created_at', 'DESC')->with([
                'orderTransaction.orderRate',
            ])
                ->find($orderJourney['order_id']);

            if (isset($order->orderTransaction->orderRate->provider) && $order->orderTransaction->orderRate->provider == "USPS") {
                array_push($orders, $order);
            };
        }

        $warehouse = Warehouse::get();

        return [
            'orders' => $orders,
            'warehouse' => $warehouse
        ];
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $data = [];

            if($request->file('file')) {
                $file = $request->file('file');
                $filename = time().'_'.$file->getClientOriginalName();
   
                // File extension
                $extension = $file->getClientOriginalExtension();
   
                $date = Carbon::now()->toDateString();
                
                // File upload location
                $location = public_path() . '/documents/' . $date;
   
                File::isDirectory($location) or File::makeDirectory($location, 0777, true, true);
   
                // Upload file
                $file->move($location,$filename);
                
                $fileKey = '/documents/'. $date . '/'. $filename;
               
   
                // File path
                $filepath = url($fileKey);
                $data['file_mainfest'] = $filepath;
            }

            $data['code'] = $request['code'];
            $data['id_warehouse'] = $request['wearhouse'];
            $data['provider'] = "USPS";
            $data['status'] = MainfestUs::NEW;
            $data['user_create'] = Auth::id();
            if (count($request['order_ids']) > 0) {
                $data['item_count']  = count($request['order_ids']);
            }

            $mainfest = MainfestUs::create($data);

            foreach ($request['order_ids'] as $order_id) {
                $mainfestDetail = [
                    'mainfest_id' => $mainfest->id,
                    'order_id' =>$order_id,
                    'user_id' => Auth::id()
                ];
                MainfestDetail::create($mainfestDetail);
                OrderJourney::where('order_id', $order_id)
                ->whereNotNull('id_packing_list')
                ->where('status', OrderJourney::PACKING)
                ->update(['is_mainfest_status'=> OrderJourney::MAINFEST_PROCESSING]);
            }
            

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }

    }
}