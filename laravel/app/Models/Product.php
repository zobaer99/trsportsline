<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['slug','name','club','category','description','price','compare_at_price','image_url','back_image_url','gallery_images','badge','edition','variants','stock','is_active','meta_title','meta_description','rating_value','review_count'];
    protected $casts = ['variants' => 'array', 'gallery_images' => 'array', 'is_active' => 'boolean', 'price' => 'integer', 'compare_at_price' => 'integer', 'stock' => 'integer', 'rating_value' => 'decimal:1', 'review_count' => 'integer'];
    public function orderItems(): HasMany { return $this->hasMany(OrderItem::class); }
}
