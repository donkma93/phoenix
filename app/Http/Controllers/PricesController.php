<?php

namespace App\Http\Controllers;

use http\Exception\BadConversionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PriceList;
use Illuminate\Support\Facades\Validator;

class PricesController extends Controller
{
    //
    public function tableDetail(Request $request, $table_id) {
        $request->flash();
        $destination = trim($request->input('destination'));
        $weight = trim($request->input('weight'));
        $data = [];
        $query = DB::table('pnx_price_tabledetails as price_details')
            ->where('id_price_table', $table_id)->orderBy('weight', 'asc');

        if (!!$destination) {
            $query = $query->where('destination', strtolower($destination));
        }

        if (!!$weight) {
            $query = $query->where('weight', '<=', $weight);
        }

        $prices = $query->paginate(20);

        $table_info = DB::table('pnx_price_table')->find($table_id);
        $weight_level = DB::table('pnx_price_tabledetails')->where('id_price_table', $table_id)->select('weight')->groupBy('weight')->get();
        $destination_list = DB::table('pnx_price_tabledetails')->where('id_price_table', $table_id)->select('destination')->groupBy('destination')->get();
        $data_exist = DB::table('pnx_price_tabledetails')->where('id_price_table', $table_id)->get()->toArray();
        $show_import_excel = false;

        if (count($data_exist) === 0) {
            $show_import_excel = true;
        }

        $data['table_id'] = $table_id;
        $data['table_info'] = $table_info ?? [];
        $data['prices'] = $prices ?? [];
        $data['weight_level'] = $weight_level ?? [];
        $data['destination_list'] = $destination_list ?? [];
        $data['show_import_excel'] = $show_import_excel;

        return view('prices.list', $data);
    }

    public function createPriceTable() {
        $tables = DB::table('pnx_price_table')->paginate(20);
        $users = DB::table('users')->pluck('email', 'id')->toArray();
        $data['tables'] = $tables;
        $data['users'] = $users;

        return view('prices.new-table', $data);
    }

    public function storePriceTable(Request $request) {
        $rules = [
            'name' => 'required|min:5|unique:pnx_price_table,name|alpha_dash',
            /*'status' => 'required',*/
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // Nếu có lỗi xảy ra

            // $validator->errors()->add('msg', 'Vui lòng kiểm tra lại dữ liệu');
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Nếu validate thành công
        $name = trim($request->input('name'));
        $user_create = Auth::id();
        $status = trim($request->input('status'));

        $result = DB::table('pnx_price_table')->insert([
            'name' => strtolower($name),
            'status' => PriceList::TABLE_ACTIVE,
            'user_create' => $user_create
        ]);

        if ($result) {
            return redirect()->route('staff.priceTable.list')->with('success', 'Table created successfully!');
        } else {
            return back()->with('error', 'Table creation failed!');
        }
    }

    public function addPrices() {
        $tables = DB::table('pnx_price_table')->get()->toArray();
        $data['tables'] = $tables;

        return view('prices.add-price', $data);
    }

    public function deletePrice(Request $request) {
        $price_id = $request->input('price_id');

        if (!!$price_id) {
            $result = DB::table('pnx_price_tabledetails')->where('id', $price_id)->delete();
            if (!!$result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Xóa bản ghi thành công!'
                ]);
                exit();
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Xóa bản ghi thất bại!'
                ]);
                exit();
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Vui lòng kiểm tra lại id của bản ghi muốn xóa!'
            ]);
        }
        exit();
    }

    public function deletePricesTable(Request $request) {
        $table_id = $request->input('table_id');

        if (!!$table_id) {
            $result = DB::table('pnx_price_table')->where('id', $table_id)->delete();
            if (!!$result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Xóa bản ghi thành công!'
                ]);
                exit();
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Xóa bản ghi thất bại!'
                ]);
                exit();
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Vui lòng kiểm tra lại id của bản ghi muốn xóa!'
            ]);
        }
        exit();
    }

    public function changeStatusTable(Request $request) {
        $table_id = $request->input('table_id');
        $status = $request->input('status');

        if (!!$table_id && isset($status)) {
            $result = DB::table('pnx_price_table')->where('id', $table_id)->update(['status' => $status]);
            if (!!$result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Status changed successfully!'
                ]);
                exit();
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Status change failed!'
                ]);
                exit();
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'The table id or status does not exist!'
            ]);
        }
        exit();
    }

    public function changePrice(Request $request) {
        $price_table_id = $request->input('price_table_id');
        // $user_id = $request->input('user_id');
        $partner_code = $request->input('partner_code');

        if (!!$price_table_id && !!$partner_code) {
            DB::table('partners')->where('partner_code', $partner_code)->update(['id_price_table' => $price_table_id]);
            //DB::update('select `pnx_price_table`.`name`, `pnx_price_table`.`id` from `users` inner join `partners` on `partners`.`partner_code` COLLATE utf8mb4_general_ci = `users`.`partner_code` COLLATE utf8mb4_general_ci inner join `pnx_price_table` on `pnx_price_table`.`id` = `partners`.`id_price_table` where `users`.`id` = ' . $id);
            return redirect()->back()->with('success', 'Changed successfully!');
        } else {
            return redirect()->back()->with('error', 'Changed failed!');
        }
    }

    public function storePrices(Request $request) {
        $rules = [
            'name' => 'required',
            'destination' => 'required|min:2',
            'weight' => 'required|integer|min:0',
            'price' => 'required|integer|min:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // Nếu có lỗi xảy ra

            // $validator->errors()->add('msg', 'Vui lòng kiểm tra lại dữ liệu');
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Nếu validate thành công
        $id_price_table = $request->input('name');
        $destination = $request->input('destination');
        $weight = $request->input('weight');
        $price = $request->input('price');

        $result = DB::table('pnx_price_tabledetails')->insert([
            'id_price_table' => $id_price_table,
            'destination' => $destination,
            'weight' => $weight,
            'price' => $price
        ]);

        if ($result) {
            return redirect()->route('staff.prices.add')->with('success', 'Added successfully!');
        } else {
            return back()->with('error', 'Add failed!');
        }
    }
}
