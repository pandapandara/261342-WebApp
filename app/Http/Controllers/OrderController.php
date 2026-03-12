<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Auth::user()->orders()->with('items', 'payments')->get();
        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::where('order_id', $id)
                      ->where('user_id', Auth::id())
                      ->firstOrFail();
        return view('orders.show', compact('order'));
    }

    /**
     * Mark order as packing
     */
    public function markAsPacking(string $id)
    {
        $order = Order::where('order_id', $id)->firstOrFail();
        
        if ($order->status !== 'processing') {
            return redirect()->route('orders.index')
                           ->with('warning', 'Order must be in processing status to pack.');
        }

        $order->markAsPacking();
        return redirect()->route('orders.index')->with('success', 'Order marked as packing.');
    }

    /**
     * Mark order as delivering
     */
    public function markAsDelivering(string $id)
    {
        $order = Order::where('order_id', $id)->firstOrFail();
        
        if ($order->status !== 'packing') {
            return redirect()->route('orders.index')
                           ->with('warning', 'Order must be in packing status to deliver.');
        }

        $order->markAsDelivering();
        return redirect()->route('orders.index')->with('success', 'Order marked as delivering.');
    }

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
}
