<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    
    public function index(Request $request)
    {

        $product = Product::with('category')
        ->when($request->search, function ($query) use ($request) { 
            $query->where('name','like', '%'. $request->search . '%')
            ->orWhereHas('category', function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            });
        })
        ->when($request->min_price, function($query) use ($request){
            $query->where('price','>=',$request->min_price);
        })
        ->when($request->max_price, function($query) use ($request) {
            $query->where('price', '<=', $request->max_price);
        })
        ->when($request->count, function ($query) use ($request) {
            $query->where('count','=',$request->count);
        })
        ->get();
        return response()->json($product);
    }

    
    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function getByCategory(Category $category){
        $products =$category->products()->get();
        return response()->json([
            'category_name' => $category->name,
            'count' => $products->count(),
            'products' => $products,
        ]);
    }

}
