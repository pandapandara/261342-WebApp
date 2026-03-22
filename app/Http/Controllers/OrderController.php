<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $orders = Auth::user()
        ->orders()
        ->with('items.product')
        ->latest('order_date') 
        ->get();

    return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        return view('orders.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
        'products' => 'required|array|min:1',
        'products.*.product_id' => 'required|exists:products,product_id',
        'products.*.quantity'  => 'required|integer|min:1',
        //collect data for order details from user input, such as name, address, phone number, payment method, etc. (for order confirmation page)
        'name'             => 'required|string',
        'address'          => 'required|string',
        'phone'            => 'required|string|max:10',
        'payment_method'   => 'required|in:promptpay,credit_card,bank_transfer,cash_on_delivery',
        ]);

       return DB::transaction(function () use ($validated) {
        // 2. Fetch products and lock records to prevent overselling
        $productIds = collect($validated['products'])->pluck('product_id');
        $products = Product::whereIn('product_id', $productIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('product_id');

        // 3. Check stock levels before doing anything
        foreach ($validated['products'] as $item) {
            $product = $products->get($item['product_id']);
            if (!$product || $product->stock_number < $item['quantity']) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'products' => "Stock not enough for " . ($product->name ?? 'Unknown Product'),
                ]);
            }
        }
        // 4. Calculate Totals
        $total = collect($validated['products'])->sum(
            fn($item) => $products[$item['product_id']]->price * $item['quantity']
        );
        $shippingFee = 50; // fixed price
        $grandTotal  = $total + $shippingFee;
        // 5. Create Order and LINK User data (Address, Phone, etc.)
        $order = Auth::user()->orders()->create([
            'status'         => 'pending',
            'total_amount'   => $grandTotal,
            'shipping_fee'   => $shippingFee,
            'order_date'     => now(),
            'customer_name'  => $validated['name'],    // ลิงก์ชื่อ
            'address'        => $validated['address'], // ลิงก์ที่อยู่
            'phone'          => $validated['phone'],   // ลิงก์เบอร์โทร
        ]);
        // 6. Map Order Items, Reduce Stock, and Save
        $orderItems = collect($validated['products'])->map(function ($item) use ($products) {
            $product = $products[$item['product_id']];
            
            // Deduct stock here
            $product->decrement('stock_number', $item['quantity']);

            return [
                'product_id'        => $product->product_id,
                'quantity'          => $item['quantity'],
                'price_at_purchase' => $product->price,
            ];
        })->toArray();
        $order->items()->createMany($orderItems);
        // 7. Create Payment Record
        $order->payments()->create([
            'status'       => 'unpaid',
            'method'       => $validated['payment_method'],
            'amount'       => $grandTotal,
            'payment_date' => now(),
        ]);

        // 8. Clear user's cart items after successful order
        Auth::user()->cart()->first()?->items()->delete();
        
        // 9. Redirect to payment page
        return redirect()->route('payments.create', $order->order_id)
                         ->with('success', 'Order created successfully');
    });
}
// For "Buy Now" functionality, we can create a separate method that creates an order directly from a single product without going through the cart.
public function storeNow(Request $request)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:products,product_id',
        'quantity'   => 'required|integer|min:1',
    ]);

    
    return redirect()->route('orders.confirm', [ 'product_id' => $request->product_id,
        'quantity'   => $request->quantity,]);
}

//need confirm order page before store order, to show order summary and confirm before place order
public function confirm(Request $request) 
{
    if ($request->has('product_id')) {
        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;

        return view('orders.confirm', [
            'items'      => collect([['product' => $product, 'quantity' => $quantity, 'product_id' => $product->product_id]]),
            'is_buy_now' => true,
            'product_id' => $product->product_id,
            'quantity'   => $quantity,
        ]);
    }

    if ($request->has('product_id')) {
        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;

        return view('orders.confirm', [
            'items'      => collect([['product' => $product, 'quantity' => $quantity, 'product_id' => $product->product_id]]),
            'is_buy_now' => true,
            'product_id' => $product->product_id,
            'quantity'   => $quantity,
        ]);
    }

    $cart = Auth::user()->cart()->with('items.product')->first();

    if (!$cart || $cart->items->isEmpty()) {
        return redirect()->route('carts.index')->with('error', 'Cart is empty.');
        return redirect()->route('carts.index')->with('error', 'Cart is empty.');
    }

    return view('orders.confirm', compact('cart'));
}

    //status pending(default) -> processing -> packing -> delivering -> complete
     /**
     * Mark order as processing (payment initiated)
     */
    public function markAsProcessing(string $id)
    {
        $order = Order::where('order_id', $id)->firstOrFail();
        if ($order->status !== 'pending') {
            return redirect()->route('orders.index')
                           ->with('warning', 'Only pending orders can be marked as processing.');
        }
        $order->markAsProcessing();
        return redirect()->route('orders.show', $order->order_id)
                         ->with('success', 'Order marked as processing successfully.');
    }
    /** 
     * Mark order as packing
     */
    public function markAsDelivering(string $id) //according to the order flow (order status), only orders in packing status can be marked as delivering
    {
        $order = Order::where('order_id', $id)->firstOrFail();
        if ($order->status !== 'packing') {
            return redirect()->back()->with('error', 'Only orders in packing status can be marked as delivering.');
        }
        $order->markAsDelivering();
        return redirect()->route('orders.show', $order->order_id)
                         ->with('success', 'Order marked as delivering successfully.');
    }
     /**
     * Update the specified resource in storage.
     */
    
    /**
     * Mark order as complete
     */
    public function markAsComplete(string $id)
    {
        $order = Order::where('order_id', $id)->firstOrFail();
        
        if ($order->status !== 'delivering') {
            return redirect()->route('orders.index')
                           ->with('warning', 'Order must be in delivering status to complete.');
        }

        $order->markAsComplete();
        return redirect()->route('orders.index')->with('success', 'Order marked as complete.');
    }
    // Mark order as paid
    public function markAsPaid(string $id)
{
    $order = Auth::user()->orders()->findOrFail($id);

     // update old  payment record instead of creating new one.
    $payment = $order->payments()->where('status', 'unpaid')->first();
    
    if ($payment) {
        $payment->update([
            'status'       => 'paid',
            'method'       => 'manual',
            'payment_date' => now(),
        ]);
    } else {
        $order->payments()->create([
            'status'       => 'paid',
            'method'       => 'manual',
            'amount'       => $order->total_amount,
            'payment_date' => now(),
        ]);
    }
     
    $order->update(['payment_status' => 'paid']);
    $order->markAsProcessing();

    return redirect()->route('orders.show', $order->order_id)
                     ->with('success', 'Payment confirmed!');
}

public function cancel(string $id)
{
    $order = Auth::user()->orders()->with('items.product')->findOrFail($id);
    //allow to cancel befor shipping
    if (!in_array(strtolower($order->status), ['pending', 'processing', 'packing'])) {
        return redirect()->back()->with('error', 'Cannot cancel order in this stage.');
    }
    if (strtolower($order->payment_status) === 'paid') {
        $order->payment_status = 'refunded'; // แก้ตรงนี้ให้เป็น refunded
        $order->payments()->where('status', 'paid')->update(['status' => 'refunded']);
    } else {
        $order->payment_status = 'cancelled';
    }
    //restock
    foreach ($order->items as $item) {
        if ($item->product) {
            $item->product->increment('stock_number', $item->quantity);
        }
    }
    
    //update
    $order->status = 'cancelled';
    $order->save();
    return redirect()->route('orders.index')
                     ->with('success', 'Order cancelled successfully.');
}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::where('order_id', $id)->firstOrFail();
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }


}
