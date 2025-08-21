<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\AdminCategoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminCategoryController extends AdminBaseController
{
    protected $categoryService;

    public function __construct(AdminCategoryService $categoryService)
    {
        parent::__construct();

        $this->categoryService = $categoryService;
    }

    /**
     * View for listing category
     *
     * @return \Illuminate\View\View
     */
    public function list(Request $request)
    {       
        $input = $request->only('name');
        $info = $this->categoryService->list($input);
        
        return view('admin.category.list', $info);
    }
}
