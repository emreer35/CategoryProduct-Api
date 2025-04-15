<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Kullanici Sadece Kendi Yorumlarini alabilir 
        return response()->json(Review::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Product $product, Request $request)
    {
        try {
            $request->validate([
                'review' => 'required|string',
                'rating' => 'required|integer|min:0|max:5'
            ]);

            $review = Review::create([
                'user_id' => Auth::user()->id,
                'product_id' => $product->id,
                'review' => $request->review,
                'rating' => $request->rating
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Yorum Basariyla Olusturuldu',
                'review' => $review
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => 'Yorum olustururken bir hata meydana geldi',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user, Review $review)
    {
        return response()->json([
            'review' => $review->load('product')
        ]);
    }
    public function getUserReviews()
    {
        $review = Review::where('user_id', auth()->user()->id)->with('product')->get();

        return response()->json([
            'review' => $review
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Product $product, Request $request, Review $review,)
    {
        Gate::authorize('update', $review);
        $updatedReview = $request->validate([
            'review' => 'required|string',
            'rating' => 'required|integer|min:0|max:5'
        ]);

        $review->update($updatedReview);

        return response()->json([
            'status' => true,
            'message' => 'Review Basariyla Guncellendi',
            'review' => $review
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        if (auth()->user()->id === $review->user_id) {
            $review->delete();
            return response()->json(['status' => true], 204);
        }
        return response()->json([
            'status'=>false,
            'message' => 'Bu Islem icin yetkiniz yok'
        ]);
    }
}
