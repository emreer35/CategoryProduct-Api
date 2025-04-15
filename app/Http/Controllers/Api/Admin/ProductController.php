<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    // kod tekrarini azaltmak icin isAdmin Middleware i kullanilabilir 
    public function index()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return response()->json(Product::all());
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
            try {
                $request->validate([
                    'category_id' => 'required|exists:categories,id',
                    'name' => 'required|string',
                    'description' => 'required|string',
                    'price' => ["required", 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
                    'count' => "integer|required|min:1",
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $product = Product::create([
                    'category_id' => $request->category_id,
                    'name' => $request->name,
                    'description' => $request->description,
                    'price' => $request->price,
                    'count' => $request->count
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Urun Basariyla Olusturuldu',
                    'product' => $product
                ]);
            } catch (\Exception $exception) {
                return response()->json([
                    'status' => false,
                    'message' => 'Urun Olusturulurken Bir Hata Olustu',
                    'error' => $exception->getMessage()
                ]);
            }
        }
        return response()->json([
            'status' => false,
            'message' => 'Kullanicinin bu islemi yapmaya yetkisi yok.'
        ], 403);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return response()->json($product);
        }
        return response()->json([
            'status' => false,
            'message' => 'Kullanicinin bu islemi yapmaya yetkisi yok.'
        ], 403);
    }

    public function getProductsByCategory(Category $category)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            $products = $category->products()->get();
            return response()->json([
                'category' => $category,
                'product_count' => $products->count(),
                'product' => $products
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
    public function update(Request $request, Product $product)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            $data = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string',
                'description' => 'required|string',
                'price' => ["sometimes", 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
                'count' => "integer|sometimes|min:1",
            ]);

            $product->update($data);
            return response()->json([
                'product' => $product
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
    public function destroy(Product $product)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            $product->delete();
            return response()->json([
                'status' => true,
                'message' => 'Urun Basariyla silindi'
            ], 204);
        }
        return response()->json([
            'status' => false,
            'message' => 'Kullanicinin bu islemi yapmaya yetkisi yok.'
        ], 403);
    }
}
