<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Services\AdminBaseServiceInterface;
use Exception;

class AdminCategoryService extends AdminBaseService implements AdminBaseServiceInterface
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
}