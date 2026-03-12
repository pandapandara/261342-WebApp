<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'payment_id';
    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'status',
        'payment_date',
        'user_id', // Add this line to allow mass assignment of user_id
    ];

    protected $attributes = [
        'status' => 'pending', // Default status is pending
    ];

    public function order() 
    { 
        return $this->belongsTo(Order::class, 'order_id', 'order_id'); 
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    //adding methods to update payment status
    // Mark payment as processing
    public function markAsProcessing()
    {
        $this->update(['status' => 'processing']);
        return $this;
    }

    // Mark payment as complete
    public function markAsComplete()
    {
        $this->update(['status' => 'complete']);
        return $this;
    }

    // Mark payment as failed
    public function markAsFailed()
    {
        $this->update(['status' => 'failed']);
        return $this;
    }

}
