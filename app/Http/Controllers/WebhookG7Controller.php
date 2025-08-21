<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WebhookG7Controller extends Controller
{
    //
    public function handleData(Request $request) {
        $obj_data = json_decode($request->getContent());

        Log::info('---------- START LOG WEBHOOK G7 ----------: \n' . '----code: ' . $obj_data->code . '\n----trackingId: ' . $obj_data->trackingId . '\n----carrier: ' . $obj_data->carrier);

        try {
            $base64_str = $obj_data->content;
            $g7_tracking_number = $obj_data->code;
            $carrier_name = $obj_data->carrier;
            $carrier_tracking_number = $obj_data->trackingId;
            $file_type = $obj_data->fileType;
            //$http_host = request()->getSchemeAndHttpHost();
            $http_host = 'https://phoenixlogistics.vn';

            Log::info('---------- START LOG WEBHOOK G7 (1)------------');

            $file_data = base64_decode($base64_str);
            $f = finfo_open();
            $mime_type = finfo_buffer($f, $file_data, FILEINFO_MIME_TYPE);
            $ext_file = explode('/', $mime_type)[1];
            $response = [
                'status' => '',
                'message' => ''
            ];
        } catch (\Exception $e) {
            Log::info('---------- START LOG WEBHOOK G7 (2) ---- Exception: ' . json_encode($e));
        }

        // Phải là file pdf thì mới lưu
        if(strtolower($ext_file) === 'pdf') {
            Log::info('---------- START LOG WEBHOOK G7 (3)------------');
            // Lưu file order vào thư mục: g7_upload phải được config trong config/filesystem.php/disk
            $label_name = time() . '-label-' . $g7_tracking_number . '.pdf';

            $save_success = Storage::disk('g7_upload')->put($label_name, base64_decode($base64_str));

            // Nếu lưu file thành công thì lưu lại path, còn không thì lưu đoạn text thông báo lỗi
            if ($save_success) {
                $label_url  = config('filesystems.disks.g7_upload')['path'] . '/' . $label_name;
            } else {
                $label_url = 'Save file error';
            }

            try {
                DB::statement("update order_rates set provider = ? where order_id = (select order_id from order_transactions where tracking_provider = ?)", [
                    $carrier_name,
                    $g7_tracking_number
                ]);
            } catch (\Exception $e) {
                $response['status'] = 'error';
                Log::error('---------- START LOG WEBHOOK G7 (4)------------ Error update order_rates table in webhook g7: ' . json_encode($e));
            }

            try { // Cập nhật bảng order_transactions
                $result = DB::table('order_transactions')
                    ->where('tracking_provider', '=', $g7_tracking_number)
                    ->update([
                        'label_url' => $http_host.$label_url,
                        'tracking_number' => $carrier_tracking_number,
                        'shipping_carrier' => $carrier_name,
                    ]);

                if (!!$result) {
                    $response['status'] = 'success';
                    echo 'Update successful--';
                } else {
                    $response['status'] = 'error';
                    echo 'Update failed--';
                }
            } catch (\Exception $e) {
                $response['status'] = 'error';
                Log::error('---------- START LOG WEBHOOK G7 (5)------------ Error update order_transactions table in webhook g7: ' . json_encode($e));
            }
        } else {
            $response['status'] = 'error';
            Log::error('---------- START LOG WEBHOOK G7 (6)------------ File type is not in the correct format.');
        }
        echo json_encode($response) . '--';
        Log::error('---------- END LOG WEBHOOK G7 -------------------------------------------------------------------------------------');
        exit('End');
    }
}
