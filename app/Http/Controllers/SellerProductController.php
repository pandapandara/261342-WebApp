<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Cloudinary\Cloudinary as CloudinaryClient;
use App\Models\Order;
class SellerProductController extends Controller
{
    // แสดงเฉพาะสินค้าของ seller คนนั้น
    public function index()
{
    $products = Auth::user()->sellerProducts;
    //if people order their products
    $orders = Order::whereHas('items.product', function ($q) {
        $q->whereHas('sellers', function ($s) {
            $s->where('users.id', Auth::id());
        });
    })->with('items.product', 'user')->latest('order_date')->get();

    return view('seller.index', compact('products', 'orders'));
}

    public function create()
    {
        $tags = \App\Models\Tag::all();
        return view('seller.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'required|string',
            'price'        => 'required|numeric|min:0',
            'stock_number' => 'required|integer|min:0',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $cloudinary = new CloudinaryClient(env('CLOUDINARY_URL'));
            $result = $cloudinary->uploadApi()->upload($request->file('image')->getRealPath());
            $validatedData['image'] = $result['secure_url'];
        }

        $product = Product::create($validatedData);

        // เชื่อม seller กับ product พร้อม tags
        $tags = $request->has('tags') ? $request->tags : [];
        Auth::user()->sellerProducts()->attach($product->product_id, ['tags' => json_encode($tags)]);

        // เชื่อม product กับ tags ใน tags table
        if ($request->has('tags')) {
            $product->tags()->sync($request->tags);
        }

        return redirect()->route('seller.index')->with('success', 'Product created successfully.');
    }

    public function edit(string $id)
    {
        // เช็คว่าเป็นสินค้าของ seller คนนี้จริงไหม
        $product = Auth::user()->sellerProducts()->where('products.product_id', $id)->firstOrFail();
        $tags = \App\Models\Tag::all();
          return view('seller.edit', compact('product', 'tags'));
    }

    public function update(Request $request, string $id)
    {
        // เช็คว่าเป็นสินค้าของ seller คนนี้จริงไหม
        $product = Auth::user()->sellerProducts()->where('products.product_id', $id)->firstOrFail();

        $validatedData = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'required|string',
            'price'        => 'required|numeric|min:0',
            'stock_number' => 'required|integer|min:0',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $cloudinary = new CloudinaryClient(env('CLOUDINARY_URL'));
            $result = $cloudinary->uploadApi()->upload($request->file('image')->getRealPath());
            $validatedData['image'] = $result['secure_url'];
        }

        $product->update($validatedData);

        // อัปเดต tags ในตาราง tags
        if ($request->has('tags')) {
            $product->tags()->sync($request->tags);
        } else {
            $product->tags()->sync([]);
        }

        // อัปเดต tags ในตาราง seller_products
        Auth::user()->sellerProducts()->updateExistingPivot($product->product_id, ['tags' => json_encode($request->input('tags', []))]);

        return redirect()->route('seller.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(string $id)
    {
        $product = Auth::user()->sellerProducts()->where('products.product_id', $id)->firstOrFail();
        Auth::user()->sellerProducts()->detach($product->product_id);
        $product->delete();

        return redirect()->route('seller.index')->with('success', 'Product deleted successfully.');
    }

    //add method order that seller can control their own product for shipping and packing
    public function orders()
{
    $orders = Order::whereHas('items.product', function ($q) {
        $q->whereHas('sellers', function ($s) {
            $s->where('users.id', Auth::id());
        });
    })->with('items.product', 'user')->latest('order_date')->get();

    return view('seller.index', compact('orders'));
}

public function markAsPacking(string $id)
{
    $order = Order::whereHas('items.product', function ($q) {
        $q->whereHas('sellers', function ($s) {
            $s->where('users.id', Auth::id());
        });
    })->findOrFail($id);

    if ($order->status !== 'processing') {
        return back()->with('error', 'Order must be in processing status.');
    }

    $order->update(['status' => 'packing']);
    return back()->with('success', 'Order marked as packing.');
}

public function markAsDelivering(string $id)
{
    $order = Order::whereHas('items.product', function ($q) {
        $q->whereHas('sellers', function ($s) {
            $s->where('users.id', Auth::id());
        });
    })->findOrFail($id);

    if ($order->status !== 'packing') {
        return back()->with('error', 'Order must be in packing status.');
    }

    $order->update(['status' => 'delivering']);
    return back()->with('success', 'Order marked as delivering.');
}
}
