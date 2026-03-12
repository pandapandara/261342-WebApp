<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tag;
use App\Models\CartItem;
class Product extends Model
{
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock_number',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_number' => 'integer',
    ];

    // Access cartItem
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_id', 'product_id');
    }

    // Access carts
    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_items', 'product_id', 'cart_id')
            ->withPivot(['quantity'])
            ->withTimestamps();
    }

    // Access orderItem
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
    }

    // Access orders
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items', 'product_id', 'order_id')
            ->withPivot(['quantity', 'price_at_purchase'])
            ->withTimestamps();
    }
    public function tags()
{
    return $this->morphToMany(Tag::class, 'taggable');
}
}
