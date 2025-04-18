<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = ['category_id','name','description','price','count'];

    public function category():BelongsTo{
        return $this->belongsTo(Category::class);
    }

    public function reviews():HasMany {
        return $this->hasMany(Review::class);
    }
    
}
