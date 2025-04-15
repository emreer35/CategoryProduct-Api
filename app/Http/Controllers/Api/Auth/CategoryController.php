<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index(Request $request)
    {
        $category = Category::when($request->search, 
        fn ($query) => 
        $query->where('name','like','%'. $request->search . '%'))->get();

        return response()->json($category);
    }

    public function show(Category $category)
    {
        return response()->json($category);
    }
   
   
}
