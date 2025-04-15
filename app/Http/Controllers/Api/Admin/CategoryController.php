<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     // kod tekrarini azaltmak icin isAdmin Middleware i kullanilabilir 
    public function index()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {

            return response()->json([
                Category::withCount('products')->get()
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Kullanicinin bu islemi yapmaya yetkisi yok.'
        ], 403);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            $request->validate([
                'name' => 'required|unique:categories,name|string'
            ]);

            $category = Category::create([
                'name' => $request->name
            ]);
            return response()->json([
                'category' => $category
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Kullanicinin bu islemi yapmaya yetkisi yok.'
        ], 403);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return response()->json([
                'category' => $category->load('products')
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Kullanicinin bu islemi yapmaya yetkisi yok.'
        ], 403);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {

            $validatedCategory = $request->validate([
                'name' => 'required|string'
            ]);

            if ($request->filled('name')) {
                $category->name = $request->name;
                $category->save();
            }
            $productCountofCategory = $category->products()->count();

            $category->load('products');
            return response()->json([
                'productCountofCategory' => $productCountofCategory,
                'updatedCategory' => $category,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Kullanicinin bu islemi yapmaya yetkisi yok.'
        ], 403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if (Auth::check() && Auth::user()->role === 'admin'){
            $category->delete();
            return response()->json([
                'status' => true,
                'message' => 'Kategori basariyla silindi'
            ], 204);
        }

        return response()->json([
            'status' => false,
            'message' => 'Kullanicinin bu islemi yapmaya yetkisi yok.'
        ], 403);
    }
}
