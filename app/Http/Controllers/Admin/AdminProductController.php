<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\AdminProductService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminProductController extends AdminBaseController
{
    protected $productService;

    public function __construct(AdminProductService $productService)
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
        
        return view('admin.product.list', $info);
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

        return view('admin.product.detail', $packages);
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
            $this->productService->uploadImage($parameters);

            return redirect()->back()->with('success', "Upload image successfully!");
        } catch(Exception $e) {
            Log::error($e);
            
            return redirect()->back()->with('fail', "Upload image fail!");
        }
    }
}
