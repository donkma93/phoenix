<?php

namespace App\Services;

use App\Models\MRequestType;
use App\Services\InvoiceServiceInterface;
use App\Models\Package;
use App\Models\PackageHistory;;
use App\Models\User;
use App\Models\MTax;
use App\Models\RequestHistory;
use App\Models\RequestTimeHistory;

use App\Models\StoragePrice;
use App\Models\UserRequest;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceService implements InvoiceServiceInterface
{
    const OUTBOUND_MIN_PRICE = 40;
    const RETURN_MIN_UNIT = 50;

    const RELABEL_MIN_UNIT = 100;
    const RELABEL_SIZE = 12;
    const RELABEL_WEIGHT = 2;

    const REMOVAL_MIN_UNIT = 100;
    const REMOVAL_SIZE = 12;
    const REMOVAL_WEIGHT = 2;

    const INBOUND_PRICE = 1;

    public function showProfile($userId, $targetMonth, $targetYear)
    {
        try {
            $user = User::with(['addresses' => function ($address) {
                $address->where('in_use', 1);
            }, 'profile'])->where('id', $userId)->first();

            $firstDayOfTargetMonth = new DateTime($targetYear . "-" . $targetMonth . "-01");
            $firstDayOfTargetNextMonth = $firstDayOfTargetMonth->modify('+1 month');

            // Price
            $price = MRequestType::with(['unitPrice' => function ($query) {
                $query->orderBy('min_unit', 'desc');
            }])->get()->mapWithKeys(function ($item) {
                return [$item->name => $item->unitPrice];
            })->toArray();

            // table `package_histories`
            //   Stored
            //   Inbound
            // table `request_histories`
            //   Outbound
            //   Relabel
            //   Return
            //   Repack
            //   Removal
            // table `request_time_histories`
            //   Warehouse Labor

            // Stored
            $packages = Package::withTrashed()
                ->where('user_id', $userId)->whereHas('histories', function ($query) use ($firstDayOfTargetNextMonth) {
                    $query->whereDate('created_at', '<', $firstDayOfTargetNextMonth)
                        ->where('status', Package::STATUS_STORED);
                })->with(['histories' => function ($query) use ($firstDayOfTargetNextMonth) {
                    $query->whereDate('created_at', '<', $firstDayOfTargetNextMonth)
                        ->orderBy('created_at', 'DESC');
                }, 'packageGroupWithTrashed'])->get();

            $storedPrices = StoragePrice::orderBy('month', 'desc')->get();

            $numberStored = 0;
            $totalPriceStored = 0;

            $priceStoredSummary = $totalStoredSummary = $storedResult = [];
            foreach ($storedPrices as $storedPrice) {
                $priceStoredSummary[$storedPrice->month] = 0;
                $totalStoredSummary[$storedPrice->month] = 0;
                $storedResult[$storedPrice->month] = [];
            }

            foreach ($packages as $package) {
                if ($package->histories[0]->status != Package::STATUS_STORED) {
                    continue;
                }

                $createdAt = $package->histories[0]->created_at;

                foreach ($package->histories as $history) {
                    if ($history->status != Package::STATUS_STORED) {
                        break;
                    }
                    $createdAt = $history->created_at;
                }

                // calculator price
                $packageCuft = ((double)$package->width * (double)$package->length * (double)$package->height) / (12 * 12 * 12);
                $packageCuft = round($packageCuft, 4);
                $cuftUnique = $package->width . "-" . $package->length . "-" . $package->height;

                $startDate = new DateTime($createdAt);
                $diffMonths = $this->diffInMonths($startDate, $firstDayOfTargetNextMonth);

                if ($diffMonths < 1) {
                    continue;
                }

                $groupId = $package->packageGroupWithTrashed->id;
                $groupName = $package->packageGroupWithTrashed->name;
                $numberStored++;

                foreach ($storedPrices as $storedPrice) {
                    // Choose first price avaiable - order by month desc
                    if ($diffMonths >= $storedPrice->month) {
                        $priceCuft = round($packageCuft * $storedPrice->price, 4);

                        if (isset($storedResult[$storedPrice->month][$groupId][$cuftUnique])) {
                            $storedResult[$storedPrice->month][$groupId][$cuftUnique]['total'] += 1;
                            $storedResult[$storedPrice->month][$groupId][$cuftUnique]['total_price'] = round($storedResult[$storedPrice->month][$groupId][$cuftUnique]['total_price'] + $priceCuft, 4);
                        } else {
                            $storedResult[$storedPrice->month][$groupId][$cuftUnique] = [
                                'total' => 1,
                                'cuft' => $packageCuft,
                                'name' => $groupName,
                                'price' => $storedPrice->price,
                                'total_price' => $priceCuft,
                            ];
                        }

                        $totalStoredSummary[$storedPrice->month] += 1;
                        $priceStoredSummary[$storedPrice->month] += $priceCuft;
                        $totalPriceStored += $priceCuft;
                        break;
                    }
                }
            }

            // Inbound
            $inboundHistories = PackageHistory::join('packages', 'package_histories.package_id', '=', 'packages.id')
                ->join('package_groups', 'packages.package_group_id', '=', 'package_groups.id')
                ->where('package_histories.status', Package::STATUS_INBOUND)
                ->whereMonth('package_histories.created_at', $targetMonth)
                ->whereYear('package_histories.created_at', $targetYear)
                ->whereNull('package_histories.deleted_at')
                ->where('packages.user_id', $userId)
                // ->where('package_groups.user_id', $userId)
                ->select('package_groups.id as id', 'package_groups.name as group_name', DB::raw("count(*) as total"))
                ->groupBy('package_groups.id')
                ->get();

            $priceInbound = $price["add package"][0]['min_size_price'];

            $numberInbound = $totalPriceInbound = 0;
            $inboundResult = [];

            foreach ($inboundHistories as $inboundHistory) {
                $count = $inboundHistory['total'];
                $priceData = $priceInbound * $count;
                $totalPriceInbound += $priceData;
                $numberInbound += $count;

                $inboundResult[] = [
                    'group_name' => $inboundHistory['group_name'],
                    'total' => $count,
                    'price' => $priceInbound,
                    'total_price' => $priceData,
                ];
            }

            // table `request_histories`
            $requestHistories = RequestHistory::join('request_packages', 'request_histories.request_package_id', '=', 'request_packages.id')
                ->join('request_package_groups', 'request_packages.request_package_group_id', '=', 'request_package_groups.id')
                ->join('package_groups', 'request_package_groups.package_group_id', '=', 'package_groups.id')
                ->join('user_requests', 'request_package_groups.user_request_id', '=', 'user_requests.id')
                ->join('m_request_types', 'user_requests.m_request_type_id', '=', 'm_request_types.id')
                ->select(
                    'request_histories.*',
                    'request_packages.package_number as rp_package_number',
                    'request_packages.unit_number as rp_unit_number',
                    'package_groups.id as group_id',
                    'package_groups.name as group_name',
                    'package_groups.unit_width as unit_width',
                    'package_groups.unit_weight as unit_weight',
                    'package_groups.unit_height as unit_height',
                    'package_groups.unit_length as unit_length',
                    'm_request_types.name as request_type',
                    'user_requests.option as option'
                )
                ->whereMonth('request_histories.created_at', $targetMonth)
                ->whereYear('request_histories.created_at', $targetYear)
                ->whereNull('request_histories.deleted_at')
                ->whereNull('request_packages.deleted_at')
                ->whereNull('request_package_groups.deleted_at')
                ->where('user_requests.user_id', $userId)
                ->whereNull('user_requests.deleted_at')
                ->get();

            // dd($requestHistories->toArray());

            // Request History
            $requestTimeHistories = RequestTimeHistory::join('user_requests', 'request_time_histories.user_request_id', '=', 'user_requests.id')
                ->join('m_request_types', 'user_requests.m_request_type_id', '=', 'm_request_types.id')
                ->whereMonth('request_time_histories.created_at', $targetMonth)
                ->whereYear('request_time_histories.created_at', $targetYear)
                ->whereNull('request_time_histories.deleted_at')
                ->where('user_requests.user_id', $userId)
                ->where('user_requests.option', UserRequest::OPTION_TIME)
                ->whereNull('user_requests.deleted_at')
                ->whereNull('m_request_types.deleted_at')
                ->where('m_request_types.name', 'warehouse labor')
                ->select(DB::raw('SUM(request_time_histories.hour) as hour'))
                ->first();

            $numberLaborHour = $requestTimeHistories->hour ?? 0;

            $numberOutbound
                = $numberLaborUnit
                = $numberRelabelMinSize = $numberRelabelMaxSize = $numberRelabelBuffer
                = $numberRepack
                = $numberReturn = $numberReturnBuffer
                = $numberRemovalMinSize = $numberRemovalMaxSize = $numberRemovalBuffer
                = 0;

            $outboundResult
                = $laborResult
                = $relabelMinResult = $relabelMaxResult
                = $repackResult
                = $returnResult
                = $removalMinResult = $removalMaxResult
                = [];

            $priceOutbound = $price["outbound"][0]['min_size_price'];
            $priceRelabels = $price["relabel"];

            foreach ($requestHistories as $requestHistory) {
                if ($requestHistory->request_type == "outbound") {
                    $count = $requestHistory->package_number;
                    $numberOutbound += $count;
                    $priceData = $priceOutbound * $count;

                    if (isset($outboundResult[$requestHistory->group_id])) {
                        $outboundResult[$requestHistory->group_id]['total'] += $count;
                        $outboundResult[$requestHistory->group_id]['total_price'] += $priceData;
                    } else {
                        $outboundResult[$requestHistory->group_id] = [
                            'group_name' => $requestHistory->group_name,
                            'total' => $count,
                            'price' => $priceOutbound,
                            'total_price' => $priceData
                        ];
                    }
                }

                if ($requestHistory->request_type == "warehouse labor") {
                    if ($requestHistory->option != UserRequest::OPTION_QUANTITY) {
                        continue;
                    }

                    $count = $requestHistory->package_number * $requestHistory->rp_unit_number;
                    $numberLaborUnit += $count;

                    if (isset($laborResult[$requestHistory->group_id])) {
                        $laborResult[$requestHistory->group_id]['total'] += $count;
                    } else {
                        $laborResult[$requestHistory->group_id] = [
                            'group_name' => $requestHistory->group_name,
                            'total' => $count,
                        ];
                    }
                }

                if ($requestHistory->request_type == "relabel") {
                    $count = $requestHistory->package_number * $requestHistory->rp_unit_number;

                    if (
                        $requestHistory->unit_weight < self::RELABEL_WEIGHT
                        && $requestHistory->unit_width < self::RELABEL_SIZE
                        && $requestHistory->unit_height < self::RELABEL_SIZE
                        && $requestHistory->unit_length < self::RELABEL_SIZE
                    ) {
                        if (isset($relabelMinResult[$requestHistory->group_id])) {
                            $relabelMinResult[$requestHistory->group_id]['total'] += $count;
                        } else {
                            $relabelMinResult[$requestHistory->group_id] = [
                                'group_name' => $requestHistory->group_name,
                                'total' => $count,
                            ];
                        }

                        $numberRelabelMinSize += $count;
                    } else {
                        // Oversize
                        if (isset($relabelMaxResult[$requestHistory->group_id])) {
                            $relabelMaxResult[$requestHistory->group_id]['total'] += $count;
                        } else {
                            $relabelMaxResult[$requestHistory->group_id] = [
                                'group_name' => $requestHistory->group_name,
                                'total' => $count,
                            ];
                        }

                        $numberRelabelMaxSize += $count;
                    }
                }

                if ($requestHistory->request_type == "return") {
                    $count = $requestHistory->unit_number;
                    $numberReturn += $count;

                    if (isset($returnResult[$requestHistory->group_id])) {
                        $returnResult[$requestHistory->group_id]['total'] += $count;
                    } else {
                        $returnResult[$requestHistory->group_id] = [
                            'group_name' => $requestHistory->group_name,
                            'total' => $count,
                        ];
                    }
                }

                if ($requestHistory->request_type == "repack") {
                    $count = $requestHistory->package_number * $requestHistory->rp_unit_number;
                    $numberRepack += $count;

                    if (isset($repackResult[$requestHistory->group_id])) {
                        $repackResult[$requestHistory->group_id]['total'] += $count;
                    } else {
                        $repackResult[$requestHistory->group_id] = [
                            'group_name' => $requestHistory->group_name,
                            'total' => $count,
                        ];
                    }
                }

                if ($requestHistory->request_type == "removal") {
                    $count = $requestHistory->unit_number;

                    if (
                        $requestHistory->unit_weight < self::REMOVAL_WEIGHT
                        && $requestHistory->unit_width < self::REMOVAL_SIZE
                        && $requestHistory->unit_height < self::REMOVAL_SIZE
                        && $requestHistory->unit_length < self::REMOVAL_SIZE
                    ) {
                        if (isset($removalMinResult[$requestHistory->group_id])) {
                            $removalMinResult[$requestHistory->group_id]['total'] += $count;
                        } else {
                            $removalMinResult[$requestHistory->group_id] = [
                                'group_name' => $requestHistory->group_name,
                                'total' => $count,
                            ];
                        }

                        $numberRemovalMinSize += $count;
                    } else {
                        if (isset($removalMaxResult[$requestHistory->group_id])) {
                            $removalMaxResult[$requestHistory->group_id]['total'] += $count;
                        } else {
                            $removalMaxResult[$requestHistory->group_id] = [
                                'group_name' => $requestHistory->group_name,
                                'total' => $count,
                            ];
                        }

                        $numberRemovalMaxSize += $count;
                    }
                }
            }

            // price Outbound
            $totalPriceOutboundOrigin = $numberOutbound * $priceOutbound;
            $totalPriceOutbound = $totalPriceOutboundBuffer = 0;

            if ($numberOutbound > 0) {
                if ($totalPriceOutboundOrigin >= self::OUTBOUND_MIN_PRICE) {
                    $totalPriceOutbound = $totalPriceOutboundOrigin;
                } else {
                    $totalPriceOutbound = self::OUTBOUND_MIN_PRICE;
                    $totalPriceOutboundBuffer = self::OUTBOUND_MIN_PRICE - $totalPriceOutboundOrigin;
                }
            }

            // price Warehouse labor
            $priceLaborHour = $price["warehouse labor"][0]['min_size_price'];
            $priceLaborUnit = $price["warehouse labor"][0]['max_size_price'];

            $totalPriceLaborHour = $numberLaborHour * $priceLaborHour;
            $totalPriceLaborUnit = $numberLaborUnit * $priceLaborUnit;

            $totalPriceLabor = $totalPriceLaborHour + $totalPriceLaborUnit;

            // price relabel
            $numberRelabel = $numberRelabelMinSize + $numberRelabelMaxSize;
            $priceRelabelMinSize = $priceRelabelMaxSize
                = $totalPriceRelabel = $totalPriceRelabelMinSize = $totalPriceRelabelMaxSize = 0;

            if ($numberRelabel > 0) {
                if ($numberRelabel < self::RELABEL_MIN_UNIT) {
                    $numberRelabelBuffer = self::RELABEL_MIN_UNIT - $numberRelabel;
                }

                if ($numberRelabelMinSize > 0) {
                    foreach ($priceRelabels as $priceRelabel) {
                        if ($numberRelabelMinSize >= $priceRelabel['min_unit']) {
                            $priceRelabelMinSize = $priceRelabel['min_size_price'];
                            $totalPriceRelabelMinSize = $numberRelabelMinSize * $priceRelabelMinSize;
                            break;
                        }
                    }
                }

                if ($numberRelabelMaxSize > 0) {
                    foreach ($priceRelabels as $priceRelabel) {
                        if ($numberRelabelMaxSize >= $priceRelabel['min_unit']) {
                            $priceRelabelMaxSize = $priceRelabel['max_size_price'];
                            $totalPriceRelabelMaxSize = $numberRelabelMaxSize * $priceRelabelMaxSize;
                            break;
                        }
                    }
                }
            }

            $totalPriceRelabelBuffer = $priceRelabelMinSize * $numberRelabelBuffer;
            $totalPriceRelabel = $totalPriceRelabelMinSize + $totalPriceRelabelMaxSize + $totalPriceRelabelBuffer;

            // price return
            $priceReturn = $price["return"][0]['min_size_price'];

            if ($numberReturn > 0 && $numberReturn < self::RETURN_MIN_UNIT) {
                $numberReturnBuffer = self::RETURN_MIN_UNIT - $numberReturn;
            }

            $totalPriceReturnOrigin = $numberReturn * $priceReturn;
            $totalPriceReturnBuffer = $numberReturnBuffer * $priceReturn;

            // price repack
            $priceRepack = $price["repack"][0]['min_size_price'];
            $totalPriceRepack = $numberRepack * $priceRepack;

            // removal
            $numberRemoval = $numberRemovalMinSize + $numberRemovalMaxSize;
            $priceRemovalMinSize = $priceRemovalMaxSize = 0;
            $totalPriceRemoval = $totalPriceRemovalMinSize = $totalPriceRemovalMaxSize = $totalPriceRemovalBuffer = 0;

            $priceRemovals = $price["removal"];
            if ($numberRemoval > 0) {
                if ($numberRemoval < self::REMOVAL_MIN_UNIT) {
                    $numberRemovalBuffer = self::REMOVAL_MIN_UNIT - $numberRemoval;
                }

                if ($numberRemovalMinSize > 0) {
                    foreach ($priceRemovals as $priceRemoval) {
                        if ($numberRemovalMinSize >= $priceRemoval['min_unit']) {
                            $totalPriceRemovalMinSize = $numberRemovalMinSize * $priceRemoval['min_size_price'];
                            $priceRemovalMinSize = $priceRemoval['min_size_price'];
                            break;
                        }
                    }
                }

                if ($numberRemovalMaxSize > 0) {
                    foreach ($priceRemovals as $priceRemoval) {
                        if ($numberRemovalMaxSize >= $priceRemoval['min_unit']) {
                            $totalPriceRemovalMaxSize = $numberRemovalMaxSize * $priceRemoval['max_size_price'];
                            $priceRemovalMaxSize = $priceRemoval['max_size_price'];
                            break;
                        }
                    }
                }
            }

            $totalPriceRemovalBuffer = $priceRemovalMinSize * $numberRemovalBuffer;
            $totalPriceRemoval = $totalPriceRemovalMinSize + $totalPriceRemovalMaxSize + $totalPriceRemovalBuffer;

            // Total
            $tax = MTax::first();

            $subTotal = $totalPriceStored
                + $totalPriceInbound
                + $totalPriceOutbound
                + $totalPriceLabor
                + $totalPriceRelabel
                + ($totalPriceReturnOrigin + $totalPriceReturnBuffer)
                + $totalPriceRepack
                + $totalPriceRemoval;

            $balance = $subTotal + ((float)$subTotal/100*((float)$tax->tax));

            return [
                'totalPriceStored' => $totalPriceStored,
                'totalPriceInbound' => $totalPriceInbound,
                'totalPriceOutbound' => $totalPriceOutbound,
                'totalPriceOutboundOrigin' => $totalPriceOutboundOrigin,
                'totalPriceOutboundBuffer' => $totalPriceOutboundBuffer,
                'totalPriceLabor' => $totalPriceLabor,
                'totalPriceLaborHour' => $totalPriceLaborHour,
                'totalPriceLaborUnit' => $totalPriceLaborUnit,
                'totalPriceRelabel' => $totalPriceRelabel,
                'totalPriceRelabelBuffer' => $totalPriceRelabelBuffer,
                'totalPriceRelabelMinSize' => $totalPriceRelabelMinSize,
                'totalPriceRelabelMaxSize' => $totalPriceRelabelMaxSize,
                'totalPriceReturn' =>  $totalPriceReturnOrigin,
                'totalPriceReturnBuffer' =>  $totalPriceReturnBuffer,
                'totalPriceRepack' => $totalPriceRepack,
                'totalPriceRemoval' => $totalPriceRemoval,
                'totalPriceRemovalMinSize' => $totalPriceRemovalMinSize,
                'totalPriceRemovalMaxSize' => $totalPriceRemovalMaxSize,
                'totalPriceRemovalBuffer' => $totalPriceRemovalBuffer,

                'priceStoredSummary' => $priceStoredSummary,
                'priceInbound' => $priceInbound,
                'priceOutbound' => $priceOutbound,
                'priceLaborHour' => $priceLaborHour,
                'priceLaborUnit' => $priceLaborUnit,
                'priceRelabelMinSize' => $priceRelabelMinSize,
                'priceRelabelMaxSize' => $priceRelabelMaxSize,
                'priceReturn' => $priceReturn,
                'priceRepack' => $priceRepack,
                'priceRemovalMinSize' => $priceRemovalMinSize,
                'priceRemovalMaxSize' => $priceRemovalMaxSize,

                'numberStored' => $numberStored,
                'totalStoredSummary' => $totalStoredSummary,
                'numberInbound' => $numberInbound,
                'numberOutbound' => $numberOutbound,
                'numberLaborHour' => $numberLaborHour,
                'numberLaborUnit' => $numberLaborUnit,
                'numberRelabel' => $numberRelabelMinSize + $numberRelabelMaxSize + $numberRelabelBuffer,
                'numberRelabelBuffer' => $numberRelabelBuffer,
                'numberRelabelMinSize' => $numberRelabelMinSize,
                'numberRelabelMaxSize' => $numberRelabelMaxSize,
                'numberReturn' =>  $numberReturn,
                'numberReturnBuffer' => $numberReturnBuffer,
                'numberRepack' => $numberRepack,
                'numberRemoval' =>  $numberRemovalMinSize + $numberRemovalMaxSize + $numberRemovalBuffer,
                'numberRemovalMinSize' => $numberRemovalMinSize,
                'numberRemovalMaxSize' => $numberRemovalMaxSize,
                'numberRemovalBuffer' => $numberRemovalBuffer,

                'storedResult' => $storedResult,
                'inboundResult' => $inboundResult,
                'outboundResult' => $outboundResult,
                'laborResult' => $laborResult,
                'relabelMinResult' => $relabelMinResult,
                'relabelMaxResult' => $relabelMaxResult,
                'repackResult' => $repackResult,
                'returnResult' => $returnResult,
                'removalMinResult' => $removalMinResult,
                'removalMaxResult' => $removalMaxResult,

                'tax' => $tax->tax,
                'balance' => $balance,
                'subTotal' => $subTotal,
                'user' => $user,
                'invoiceDate' => date('Y-m-d'),
                'targetMonth' => $targetMonth,
                'targetYear' => $targetYear
            ];
        }  catch(Exception $e) {
            Log::error($e);
            return null;
        }
    }

    function getAllUser()
    {
        $users = User::where('role', User::ROLE_USER)->get();

        return $users;
    }

    function getAllAdmin()
    {
        $admins = User::where('role', User::ROLE_ADMIN)->get();

        return $admins;
    }

    /**
     * Calculate the difference in months between two datess
     *
     * @param DateTime $start
     * @param DateTime $end
     * @return int
     */
    public function diffInMonths(DateTime $start, DateTime $end)
    {
        $diff =  $end->diff($start);

        $monthBonus = ($diff->d > 0 || $diff->h > 0 || $diff->i > 0 || $diff->s > 0 || $diff->f > 0) ? 1 : 0;

        $months = $diff->y * 12 + $diff->m + $monthBonus;
        // $months = $diff->y * 12 + $diff->m + $diff->d / 30;

        return (int) round($months);
    }
}
