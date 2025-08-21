<?php

namespace App\Http\Controllers\Staff;

use App\Services\Staff\StaffPackageGroupService;
use App\Http\Requests\Staff\CreatePackageRequest;
use App\Http\Requests\Staff\CreatePackageGroupRequest;
use App\Http\Requests\Staff\UpdatePackageGroupRequest;
use App\Http\Requests\Staff\CreateProductFromPackageGroupRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StaffPackageGroupController extends StaffBaseController
{
    protected $packageService;

    public function __construct(StaffPackageGroupService $packageService)
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
        $input = $request->only('name', 'barcode', 'email', 'onlyDeleted');
        $info = $this->packageService->list($input);

        return view('staff.package-group.list', $info);
    }

    
     /**
     * View for add new package to group
     *
     * @return \Illuminate\View\View
     */
    public function detail(Request $request, $id) 
    {   
        $parameter = $request->all();
        $info = $this->packageService->getDetailForNewPackage($parameter, $id);

        return view('staff.package-group.detail', $info);
    }

    /**
     * View for add new package to group
     *
     * @return \Illuminate\View\View
     */
    public function new(Request $request) 
    {   
        $info = $this->packageService->new();

        return view('staff.package-group.new', $info);
    }

    /**
     * Add new package
     *
     * @param App\Http\Requests\Staff\CreatePackageGroupRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(CreatePackageGroupRequest $request) 
    {   
        try {
            $parameters = $request->all();
            $newGroup = $this->packageService->create($parameters);

            if(isset($parameters['redirect'])) {
                return redirect()->route('staff.package-group.detail', ['id' => $newGroup['id']]);
            }
            return redirect()->back()->with('success', "Create package group successfully!");
        } catch(Exception $e) {
            Log::error($e);
           
            return redirect()->back()->with('fail', "Create package group fail!");
        }
    }

    /**
     * Add new package
     *
     * @param App\Http\Requests\Staff\CreatePackageRequest $request
     * @return \Illuminate\Http\Response
     */
    public function addPackage(CreatePackageRequest $request)
    {
        try {
            $parameters = $request->all();
            $this->packageService->insertPackage($parameters);

            return redirect()->back()->with('success', "Create package successfully!")->with('oldInput', $parameters);
        } catch(Exception $e) {
            Log::error($e);
          
            return redirect()->back()->with('fail', "Create package fail!")->withInput();
        }
    }
    
    /**
     * Add new package
     *
     * @param App\Http\Requests\Staff\UpdatePackageGroupRequest $request
     * @return \Illuminate\Http\Response
     */
    public function updateGroup(UpdatePackageGroupRequest $request)
    {
        try {
            $parameters = $request->only('unit_width', 'unit_length', 'unit_height', 'unit_weight', 'id', 'name', 'barcode');
            $message = $this->packageService->updatePackageGroup($parameters);

            if($message != null) {
                return redirect()->back()->with('fail', $message)->withInput();
            }

            return redirect()->back()->with('success', "Update package group successfully!")->with('oldInput', $parameters);
        } catch(Exception $e) {
            Log::error($e);
            
            return redirect()->back()->with('fail', "Update package group fail!")->withInput();
        }
    }

    /**
     * Create new product from package-group
     *
     * @param App\Http\Requests\Staff\CreateProductFromPackageGroupRequest $request
     * @return \Illuminate\Http\Response
     */
    public function createProduct(CreateProductFromPackageGroupRequest $request)
    {
        try {
            $parameters = $request->only('id');
            $id = $this->packageService->createProduct($parameters);
            
            return redirect()->route('staff.product.detail', ['id' => $id])->with('success', "Create product successfully!")->with('oldInput', $parameters);
        } catch(Exception $e) {
            Log::error($e);
            
            return redirect()->back()->with('fail', "Create prodoct fail!")->withInput();
        }
    }
}
