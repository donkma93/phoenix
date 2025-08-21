<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use App\Services\AdminBaseServiceInterface;
use App\Models\User;
use App\Models\Package;
use App\Models\UserRequest;
use App\Models\WarehouseArea;
use App\Models\PackageGroup;
use App\Models\RequestGroup;
use App\Models\RequestPackageGroup;
use App\Models\RequestPackageImage;
use App\Models\RequestPackageTracking;
use App\Models\RequestHistory;
use App\Models\MRequestType;
use App\Models\RequestTimeHistory;
use App\Models\RequestWorkingTime;
use App\Notifications\UserRequestDone;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

class AdminRequestService extends AdminBaseService implements AdminBaseServiceInterface
{
    function list($input) {
        $groupNames = [];
        $requestQuery = UserRequest::with('user', 'mRequestType')->whereHas('user', function($users) {
            $users->where('role', User::ROLE_USER)->withTrashed();
        })->orderByDesc('updated_at');

        if (isset($input['status'])) {
            $requestQuery = $requestQuery->where('status', $input['status']);
        }

        if (isset($input['type'])) {
            $requestQuery = $requestQuery->whereHas('mRequestType', function ($query) use ($input) {
                $query->where('name', $input['type']);
            });
        }

        if (isset($input['email'])) {
            $requestQuery = $requestQuery->whereHas('user', function ($query) use ($input) {
                $query->where('email', 'like', '%'.$input['email'].'%');
            });
        }

        $requestQuery = $requestQuery->paginate()->withQueryString();

        foreach($requestQuery as $request) {
            $groupName = RequestPackageGroup::where('user_request_id', $request['id'])
            ->leftJoin('package_groups', 'request_package_groups.package_group_id', '=', 'package_groups.id')
            ->select(DB::raw('GROUP_CONCAT(package_groups.name SEPARATOR ", ") AS name'))->groupBy('request_package_groups.user_request_id')->first();

            $groupNames[$request['id']] = $groupName->name;
        }

        $requestTypes = MRequestType::pluck('name')->toArray();

        $emails = User::where('role', User::ROLE_USER)->withTrashed()->pluck('email')->toArray();

        return [
            'requests' => $requestQuery,
            'requestTypes' => $requestTypes,
            'emails' => $emails,
            'oldInput' => $input,
            'groupNames' => $groupNames
        ];
    }

    function detail($id, $input) {
        $userRequest = UserRequest::with('mRequestType',  'user')->has('user')->find($id);
        $packages = [];
        $unitNeed = 0;
        $packagesList = explode(',', $userRequest->packages) ?? [];
        $packageGroupImages = [];
        $packageGroupIds = [];
        $warehouseAreaInUse = [];
        $trackings = [];
        $requestHistory = [];
        $packageGroups = [];

        $packagesSaved = Package::whereIn('packages.id', $packagesList)
        ->leftJoin(DB::raw('(select package_details.package_id as id, GROUP_CONCAT(package_groups.name SEPARATOR ", ") AS name 
        from package_details left join package_groups on package_details.package_group_id = package_groups.id
        group by package_details.package_id) as package_group_names'), 'packages.id', '=','package_group_names.id')
        ->leftJoin('package_groups', 'packages.package_group_id', '=','package_groups.id')
        ->select('packages.*', 
        'package_group_names.name as detail_groups_name', 
        'package_groups.name as group_name from packages')
        ->withTrashed()->orderByDesc('created_at')->get();
        
        if($userRequest->mRequestType->name == 'add package' && isset($userRequest->is_allow)) {
            $packages = Package::where('user_request_id', $userRequest['id'])->paginate()->withQueryString();
            $packageGroups = RequestPackageGroup::where('user_request_id', $userRequest['id'])->with('packageGroup')->paginate()->withQueryString();

            foreach($packageGroups as $package) {
                if(!in_array($package['id'], $packageGroupIds)) {
                    array_push($packageGroupIds, $package['id']);
                    $packageImage = RequestPackageImage::where('request_package_group_id', $package['id'])->pluck('image_url')->toArray();
                    $packageGroupImages[$package['id']] = $packageImage;
                }

                $tracking = RequestPackageTracking::where('request_package_group_id', $package['id'])->select(DB::raw('GROUP_CONCAT(tracking_url) AS tracking'), 'request_package_group_id')->groupBy('request_package_group_id')->first();
                if(isset($tracking)) {
                    $trackings[$package['id']] = str_replace(',', '||',$tracking->tracking);
                }
            }

            foreach($packages as $package) {
                $history = RequestHistory::where('package_id', $package['id'])->with('staff')->get();

                $requestHistory[$package['id']] = $history;
            }
        } else {
            $packages = RequestPackageGroup::where('user_request_id', $userRequest['id'])
            ->join('request_packages', 'request_package_groups.id', '=', 'request_packages.request_package_group_id')
            ->join('package_groups', 'package_groups.id', '=', 'request_package_groups.package_group_id')
            ->select('request_packages.id',
            'request_package_groups.id as request_package_group_id',
            'request_package_groups.package_group_id',
            'request_packages.package_number',
            'request_packages.unit_number',
            'request_packages.received_package_number',
            'request_packages.received_unit_number',
            'request_package_groups.barcode',
            'request_package_groups.file',
            'request_package_groups.is_insurance',
            'request_package_groups.insurance_fee',
            'request_package_groups.ship_mode',
            'request_packages.height',
            'request_packages.length',
            'request_packages.width',
            'request_packages.weight',
            'package_groups.name');

            $packages = $packages->orderByDesc('request_package_groups.created_at')->paginate()->withQueryString();

            foreach($packages as $package) {
                if(!in_array($package['request_package_group_id'], $packageGroupIds)) {
                    array_push($packageGroupIds, $package['request_package_group_id']);
                    $packageImage = RequestPackageImage::where('request_package_group_id', $package['request_package_group_id'])->pluck('image_url')->toArray();
                    $packageGroupImages[$package['request_package_group_id']] = $packageImage;
                }

                $tracking = RequestPackageTracking::where('request_package_group_id', $package['request_package_group_id'])->select(DB::raw('GROUP_CONCAT(tracking_url) AS tracking'), 'request_package_group_id')->groupBy('request_package_group_id')->first();
                if(isset($tracking)) {
                    $trackings[$package['id']] = str_replace(',', '||',$tracking->tracking);
                }

                $history = RequestHistory::where('request_package_id', $package['id'])->with('staff')->get();

                $requestHistory[$package['id']] = $history;
            }

        }

        $requestHour = RequestTimeHistory::where('user_request_id', $userRequest['id'])->first();

        $workingTimes = RequestWorkingTime::where('user_request_id', $userRequest['id'])->orderByDesc('created_at')->get();

        return  [
            'userRequest' => $userRequest,
            'packages' => $packages,
            'oldInput' => $input,
            'unitNeed' => $unitNeed,
            'packagesSaved' => $packagesSaved,
            'packageGroupImages' => $packageGroupImages,
            'trackings' => $trackings,
            'requestHistory' => $requestHistory,
            'requestHour' => $requestHour,
            'workingTimes' => $workingTimes,
            'packageGroups' => $packageGroups
        ];
    }
}
