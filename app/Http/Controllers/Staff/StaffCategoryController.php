<?php

namespace App\Http\Controllers\Staff;

use App\Http\Requests\Staff\UpdateCategoryRequest;
use App\Http\Requests\Staff\CreateCategoryRequest;
use App\Services\Staff\StaffCategoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StaffCategoryController extends StaffBaseController
{
    protected $categoryService;

    public function __construct(StaffCategoryService $categoryService)
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
        
        return view('staff.category.list', $info);
    }

    /**
     * View for create new category
     *
     * @return \Illuminate\View\View
     */
    public function new(Request $request)
    {       
        return view('staff.category.new');
    }

    /**
     * Create category
     *
     * @param App\Http\Requests\Staff\CreateCategoryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(CreateCategoryRequest $request)
    {       
        try {
            $parameters = $request->all();
            
            $id = $this->categoryService->create($parameters);
            
            return redirect()->back()->with('success', "Create category successfully!");
        } catch(Exception $e) {
            Log::error($e);

            return redirect()->back()->with('fail', "Create category fail!");
        }
    }

    /**
     * Update category
     *
     * @param App\Http\Requests\Staff\UpdateCategoryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request)
    {
        try {
            $parameters = $request->all();
            $this->categoryService->update($parameters);
            
            return redirect()->back()->with('success', "Update category successfully!");
        } catch(Exception $e) {
            Log::error($e);
    
            return redirect()->back()->with('fail', "Update category fail!");
        }
    }
}
