<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    
    public function show(User $user, Review $review) {
        return auth()->user()->id === $review->user_id;
    }
    
    public function viewAny( User $authUser){
        return true;
    }
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }
    
}
