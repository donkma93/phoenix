<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\KitComponent;
use App\Models\ProductType;
use App\Services\AdminBaseServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminProductService extends AdminBaseService implements AdminBaseServiceInterface
{
    function list($request)
    {
        $products = Product::with(['user' => function ($user) {
            $user->withTrashed();
         }, 'category' => function ($category) {
            $category->withTrashed();
         }])->has('user');

        if(isset($request['email'])) {
            $products->whereHas('user', function ($query) use ($request) {
                $query->where('email', 'like', '%'.$request['email'].'%');
            });
        }

        if(isset($request['category'])) {
            $products->whereHas('category', function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request['category'].'%');
            });
        }

        if(isset($request['name'])) {
            $products->where('name', 'like', '%'.$request['name'].'%');
        }

        if(isset($request['status'])) {
            $products->where('status', $request['status']);
        }

        if(isset($request['onlyDeleted'])) {
            if($request['onlyDeleted'] == 1) {
                $products = $products->onlyTrashed();
            }
        } else {
            $products = $products->withTrashed();
        }

        $products = $products->orderByDesc('updated_at');

        $products = $products->paginate()->withQueryString();

        $users = User::where('role', User::ROLE_USER)->pluck('email')->toArray();

        $categories = Category::pluck('name')->toArray();

        return [
            'oldInput' => $request,
            'products' => $products,
            'users' => $users,
            'categories' => $categories
        ];
    }

    function detail($id) 
    {
        $product = Product::with(['user' => function ($user) {
            $user->withTrashed();
        }, 'category' => function ($category) {
            $category->withTrashed();
        }])->has('user')->withTrashed()->find($id);

        $users = User::where('role', User::ROLE_USER)->pluck('email')->toArray();

        $categories = Category::pluck('name')->toArray();

        $components = KitComponent::where("product_id", $id)->with('component')->get();

        return [
            'product' => $product,
            'users' => $users,
            'categories' => $categories,
            'components' => $components,
        ];
    }

    function uploadImage($request) 
    {
        if(!isset($request['id'])) { 
            throw new Exception("Wrong id");
        }

        if(isset($request['image'])) {
            $image = $request['image']->move('imgs' . DIRECTORY_SEPARATOR . Product::IMG_FOLDER, cleanName($request['image']->getClientOriginalName()));

            $product = Product::find($request['id']);
            $product->image_url = $image;
            $product->save();
        }
    }
}