<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'order_id';

    protected $fillable = ['user_id', 'status', 'total_amount', 'order_date'];

    protected $casts = [
        'order_date' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    protected $attributes = [
        'status' => 'pending', // Default status is pending
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    // Access products
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items', 'order_id', 'product_id')
                    ->withPivot(['quantity', 'price_at_purchase'])
                    ->withTimestamps();
    }

    public function payments() 
    { 
        return $this->hasMany(Payment::class, 'order_id', 'order_id'); 
    }

    // Mark order as processing (payment initiated)
    public function markAsProcessing()
    {
        $this->update(['status' => 'processing']);
        return $this;
    }

    // Mark order as packing
    public function markAsPacking()
    {
        $this->update(['status' => 'packing']);
        return $this;
    }

    // Mark order as delivering
    public function markAsDelivering()
    {
        $this->update(['status' => 'delivering']);
        return $this;
    }

    // Mark order as complete
    public function markAsComplete()
    {
        $this->update(['status' => 'complete']);
        return $this;
    }

    // Mark order as failed
    public function markAsFailed()
    {
        $this->update(['status' => 'order fail']);
        return $this;
    }
}
