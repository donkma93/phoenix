<?php

namespace App\Services\Staff;

use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Services\StaffBaseServiceInterface;
use Exception;

class StaffCategoryService extends StaffBaseService implements StaffBaseServiceInterface
{
    function list($request)
    {
        $categories = Category::withTrashed();

        if(isset($request['name'])) {
            $categories->where('name', 'like', '%'.$request['name'].'%');
        }

        $categories = $categories->orderByDesc('updated_at');

        $categories = $categories->paginate()->withQueryString();

        $categoryNames = Category::pluck('name')->toArray();

        return [
            'oldInput' => $request,
            'categories' => $categories,
            'categoryNames' => $categoryNames
        ];
    }

    function create($request)
    {
        Category::create([
            'name' => $request['name']
        ]);
    }

    function update($request)
    {
        $category = Category::find($request['id']);
        $category->name = $request['name'];
        $category->save();
    }
}