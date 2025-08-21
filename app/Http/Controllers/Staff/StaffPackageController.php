<?php

namespace App\Http\Controllers\Staff;

use App\Services\Staff\StaffPackageService;
use App\Http\Requests\Staff\UpdatePackageRequest;
use App\Http\Requests\Staff\PackageCreateWithGroupRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StaffPackageController extends StaffBaseController
{
    protected $packageService;

    public function __construct(StaffPackageService $packageService)
    {
        parent::__construct();

        $this->packageService = $packageService;
    }

    /**
     * View for listing package group
     *
     * @return \Illuminate\View\View
     */
    public function list(Request $request)
    {       
        $input = $request->only('name', 'barcode', 'email', 'warehouse', 'group', 'unit', 'status', 'onlyDeleted');
        $info = $this->packageService->list($input);     
        return view('staff.package.list', $info);
    }

    /**
     * Show list package for outbound
     *
     * @return \Illuminate\View\View
     */
    public function outbound(Request $request)
    {
        try {
            if(isset($request['email']) && $request->session()->get('userId') != $request['email']) {
                $request->session()->put('userId', $request['email']);
                $request->session()->put('packageIds', []);
                $request->session()->put('address', []);
            }

            $packagesRes = $this->packageService->getPackagesByUser($request);

            return view('staff.package.outbound', [
                'packagesRes' => $packagesRes,
                'users' => $this->users,
            ]);
        } catch(Exception $e) {
            Log::error($e);
            
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * View for create new package
     *
     * @return \Illuminate\View\View
     */
    public function new(Request $request)
    {       
        $info = $this->packageService->new();
        return view('staff.package.new' , $info);
    }

    /**
     * Create package with group
     *
     * @param App\Http\Requests\Staff\PackageCreateWithGroupRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(PackageCreateWithGroupRequest $request)
    {       
        try {
            $parameters = $request->all();
            
            $this->packageService->create($parameters);

            return redirect()->back()->with('success', "Create package successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Create package fail!");
        }
    }

    /**
     * Display a detail of package.
     * 
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function detail(Request $request, $id) 
    {
        $packages = $this->packageService->detail($id);

        return view('staff.package.detail', $packages);
    }

    /**
     * Save package_id, address, user_package_id to session
     *
     * @return \Illuminate\Http\Response
     */
    public function savePackageIds(Request $request)
    {
        $parameters = $request->all();

        $id = $parameters['id'];
        $address = $parameters['address'];

        if(!empty($request->session()->get('packageIds')))
        {
            $listId = $request->session()->get('packageIds');
            $listAddress = $request->session()->get('address');
            if($parameters['isAdd'] == "true") {
                if(!in_array($id, $listId)) {
                    $request->session()->put('packageIds', array_merge($listId, [$id]));
                    $request->session()->put('address', array_merge($listAddress, [$address]));
                }
            } else {
                for($i=0;$i<count($listId);$i++) {
                    if($listId[$i] == $id) {
                        unset($listId[$i]);
                        unset($listAddress[$i]);
                    }
                }

                $request->session()->put('packageIds', $listId);
                $request->session()->put('address', $listAddress);
            }
        } else {
            $request->session()->put('packageIds', [$parameters['id']]);
            $request->session()->put('address', [$parameters['address']]);
        }
    }

    /**
     * Update Package status, address and clear session
     *
     * @return \Illuminate\Http\Response
     */
    public function updatePackageStatus(Request $request)
    {
        $packagesIds = $request->session()->get('packageIds');
        $address = $request->session()->get('address');

        try {
            $this->packageService->setOutboundPackage($packagesIds, $address);
        
            $request->session()->put('packageIds', []);
            $request->session()->put('address', []);
        } catch(Exception $e) {
            Log::error($e);
        }
    }

    /**
     * Get group package
     *
     * @return \Illuminate\Http\Response
     */
    public function getGroup(Request $request)
    {
        $parameters = $request->only('email', 'name');
        $groups = $this->packageService->getGroup($parameters);

        return $groups;
    }

    /**
     * Get group package
     *
     * @param App\Http\Requests\Staff\UpdatePackageRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePackageRequest $request)
    {
        try {
            $parameters = $request->only('id', 'weight_staff', 'height_staff', 'length_staff', 'width_staff', 'status', 'warehouse', 'barcode');
            $message = $this->packageService->update($parameters);

            if($message != null) {
                return redirect()->back()->with('fail', $message);
            }

            return redirect()->back()->with('success', "Update package successfully!");
        } catch(Exception $e) {
            Log::error($e);
            
            return redirect()->back()->with('fail', "Update package fail!");
        }
    }
}
