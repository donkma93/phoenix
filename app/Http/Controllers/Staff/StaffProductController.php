<?php

namespace App\Http\Controllers\Staff;

use App\Services\Staff\StaffProductService;
use App\Http\Requests\Staff\UpdateProductRequest;
use App\Http\Requests\Staff\CreateProductRequest;
use App\Http\Requests\Staff\CreateSKURequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StaffProductController extends StaffBaseController
{
    protected $productService;

    public function __construct(StaffProductService $productService)
    {
        parent::__construct();

        $this->productService = $productService;
    }

    /**
     * View for listing product
     *
     * @return \Illuminate\View\View
     */
    public function list(Request $request)
    {       
        $input = $request->only('name', 'category', 'email', 'status', 'onlyDeleted');
        $info = $this->productService->list($input);
        
        return view('staff.product.list', $info);
    }

    /**
     * View for create new product
     *
     * @return \Illuminate\View\View
     */
    public function new(Request $request)
    {       
        $info = $this->productService->new();

        return view('staff.product.new' , $info);
    }

    /**
     * Create product
     *
     * @param App\Http\Requests\Staff\CreateProductRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(CreateProductRequest $request)
    {       
        try {
            $parameters = $request->all();
            
            $id = $this->productService->create($parameters);
            
            return redirect()->route('staff.product.detail', ['id' => $id])->with('success', "Create product successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Create product fail!");
        }
    }

    public function createAttr($product_id)
    {
        $info = $this->productService->createAttr($product_id);
        return view('staff.product.create_attribute', compact('product_id'));
    }

    public function storeAttr(Request $request)
    {
        $this->productService->storeAttribute($request);
        return redirect()->route('staff.product.list');
    }

    /**
     * Display a detail of product
     * 
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function detail(Request $request, $id) 
    {
        $packages = $this->productService->detail($id);

        return view('staff.product.detail', $packages);
    }

    /**
     * Update product
     *
     * @param App\Http\Requests\Staff\UpdateProductRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request)
    {
        try {
            $parameters = $request->all();
            $this->productService->update($parameters);

            return redirect()->back()->with('success', "Update product successfully!");
        } catch(Exception $e) {
            Log::error($e);
            
            return redirect()->back()->with('fail', "Update product fail!");
        }
    }
    
    /**
     * Create SKU
     *
     * @param App\Http\Requests\Staff\CreateSKURequest $request
     * @return \Illuminate\Http\Response
     */
    public function createSKU(CreateSKURequest $request)
    {
        try {
            $parameters = $request->all();
            $this->productService->createSKU($parameters);

            return redirect()->back()->with('success', "Update product successfully!");
        } catch(Exception $e) {
            Log::error($e);
            
            return redirect()->back()->with('fail', "Update product fail!");
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
            $this->productService->createKitComponent($parameters);

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
            $this->productService->updateKitComponent($parameters);

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
            $this->productService->deleteKitComponent($parameters);

            return true;
        } catch(Exception $e) {
            Log::error($e);
            
            return false;
        }
    }

    public function getAttributes($sku)
    {
        $info = $this->productService->getProductAttributeBySKU($sku);
        if ($info['MESSAGE_CODE'] != "SUCCESS") {
            return response($info, 400);
        }

        return response()->json($info);
    }
}
