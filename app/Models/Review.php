<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $table = 'rewiewvs';
    protected $fillable = [
        'user_id',
        'product_id',
        'review'
    ];

    public function product():BelongsTo {
        return $this->belongsTo(Product::class);
    }
    public function user():BelongsTo {
        return $this->belongsTo(User::class);
    }
}
