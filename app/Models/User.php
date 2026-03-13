<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
   
    protected $fillable = [
        'name',
        'email',
        'password',
        'birthdate', // Add this line to allow mass assignment of birthdate
        'address', // Add this line to allow mass assignment of address
        'phone_number', // Add this line to allow mass assignment of phone_number
        'image', // Add this line to allow mass assignment of image
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthdate' => 'date', // Add this line to cast birthdate as a date
        ];
    }

    public function cart() //1 user has one cart //add many item pass cartItem
    {
        return $this->hasOne(Cart::class, 'user_id', 'id');
    }

    public function latestCart() //fetching latest/current cart
    {
        return $this->hasOne(Cart::class, 'user_id', 'id')->latestOfMany('cart_id');
    }

    public function orders() //1 User many orders
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

//nx:adding payment relationship to orders
    public function payments() //1 user has many payments through orders
    {
        return $this->hasManyThrough(Payment::class, Order::class, 'user_id', 'order_id', 'id', 'order_id');
    }
}
