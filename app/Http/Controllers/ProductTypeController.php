<?php

namespace App\Http\Controllers;

use App\Services\Staff\ProductTypeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductTypeController extends Controller
{

    protected $productTypeService;
    public function __construct(ProductTypeService $productTypeService)
    {
        $this->productTypeService = $productTypeService;
    }

    public function index(Request $request)
    {
        $productTypes = $this->productTypeService->index($request);
        return view('staff.product_type.index', compact('productTypes'));
    }

    public function create()
    {
        Log::info('Product Type create controller');
        return view('staff.product_type.create');
    }

    public function store(Request $request)
    {
        $this->productTypeService->store($request);
        return redirect()->route('staff.product-type.index');
    }

    public function createAttr($product_type_id)
    {
        $info = $this->productTypeService->createAttr($product_type_id);
        return view('staff.product_type.create_attribute', $info);
    }

    public function storeAttr(Request $request)
    {
        $this->productTypeService->storeAttr($request);
        return redirect()->route('staff.product-type.index');
    }
}
