<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    
    public function index()
    {
        return response()->json(Product::with('category')->get());
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
