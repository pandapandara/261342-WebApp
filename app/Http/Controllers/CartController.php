<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $cart = Auth::user()->cart()->with('items.product')->first();
        return view('carts.index', compact('cart'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
          
        $validated = $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'quantity'   => 'required|integer|min:1',
        ]);

        //check stock before add to cart
        $product = Product::findOrFail($validated['product_id']);
     
        if ($product->stock_number < $validated['quantity']) {
            return back()->with('error', 'สินค้าไม่เพียงพอ');
        }
       $cart = Auth::user()->cart()->firstOrCreate([
    'user_id' => Auth::id() 
]);
        $cartItem = $cart->items()->where('product_id', $validated['product_id'])->first();
        if ($cartItem) {
            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
        } else {
            $cart->items()->create($validated);
        }
        return redirect()->route('carts.index')->with('success', 'Product added to cart successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart     = Auth::user()->cart()->firstOrFail();
        $cartItem = $cart->items()->findOrFail($id);

        // ตรวจ stock
        if ($cartItem->product->stock_number < $validated['quantity']) {
            return back()->with('error', 'No product in stock');
        }

        $cartItem->update(['quantity' => $validated['quantity']]);

        return back()->with('success', 'Cart updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cart = Auth::user()->cart()->firstOrFail();
        $cart->items()->where('cart_item_id', $id)->delete();
        return redirect()->route('carts.index')->with('success', 'Product removed from cart successfully!');
    }
}
