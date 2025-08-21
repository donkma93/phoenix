<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\StorePackageGroupRequest;
use App\Services\User\UserPackageGroupService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserPackageGroupController extends UserBaseController
{
    protected $packageGroupService;

    public function __construct(UserPackageGroupService $packageGroupService)
    {
        parent::__construct();
        $this->packageGroupService = $packageGroupService;
    }

    /**
     * Show user package list.
     *
     * Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $parameters = $request->only('name', 'barcode');
            $items =  $this->packageGroupService->index($parameters);
            return view('user.package_group.index', $items);
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Show user package detail.
     *
     * @param  int  $packageId
     * @return \Illuminate\View\View
     */
    public function show($packageId)
    {
        try {
            $items =  $this->packageGroupService->show($packageId);
            return view('user.package_group.show', $items);
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Show the form to create a new user request.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        try {
            return view('user.package_group.create');
        } catch(Exception $e) {
            Log::error($e);
            //TODO redirect to error page
            abort(500);
        }
    }

    /**
     * Upload image
     *
     * @param AIlluminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    function uploadImage(Request $request)
    {
        try {
            $parameters = $request->all();
            $this->packageGroupService->uploadImage($parameters);

            return redirect()->back()->with('success', "Upload image successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Upload image fail!");
        }
    }

    /**
     * Store a new user request.
     *
     * @param  App\Http\Requests\User\StorePackageGroupRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePackageGroupRequest $request)
    {
        try {
            $this->packageGroupService->store($request->all());
            return redirect()->route('requests.create')->with('success', "Create new package group successfully");
        } catch(Exception $e) {
            Log::error($e);
            return redirect()->route('requests.create')->with('fail', "Create new package group failed");
        }
    }

    public function apiCreateProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:package_groups,name,NULL,id,deleted_at,NULL,user_id,' . Auth::id(),
            'barcode' => 'required|string|max:255',
            'unit_height' => 'nullable|numeric|min:0|not_in:0',
            'unit_width' => 'nullable|numeric|min:0|not_in:0',
            'unit_length' => 'nullable|numeric|min:0|not_in:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ]);
        }

        // Retrieve the validated input...
        $validated = $validator->validated();

        try {
            $sku = $this->packageGroupService->store($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Created product successfully',
                'sku' => $sku
            ]);
        } catch(Exception $e) {
            Log::error('Create product using api failed: ' . json_encode($request->all()));
            return response()->json([
                'status' => 'error',
                'message' => 'Create product failed: ' . $e->getMessage(),
                'sku' => null
            ]);
        }
    }

    /**
     * Create component
     *
     * @return \Illuminate\Http\Response
     */
    public function createKitComponent(Request $request)
    {
        try {
            $parameters = $request->all();
            $this->packageGroupService->createKitComponent($parameters);

            return redirect()->back()->with('success', "Create component successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Create component fail!");
        }
    }

    /**
     * Update component
     *
     * @return \Illuminate\Http\Response
     */
    public function updateKitComponent(Request $request)
    {
        try {
            $parameters = $request->all();
            $this->packageGroupService->updateKitComponent($parameters);

            return true;
        } catch(Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Delete component
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteKitComponent(Request $request)
    {
        try {
            $parameters = $request->all();
            $this->packageGroupService->deleteKitComponent($parameters);

            return true;
        } catch(Exception $e) {
            Log::error($e);

            return false;
        }
    }
}
