<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::whereHas('order', function ($query) {
                $query->where('user_id', Auth::id());
            })->with('order')->get();

        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('payments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'amount'   => 'required|numeric',
            'method'   => 'required|string',
            'status'   => 'required|string',
        ]);

        $order = Auth::user()->orders()->findOrFail($validated['order_id']);

        $order->payment()->create($validated);

        return redirect()->route('payments.index')
                         ->with('success', 'Payment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payment = Payment::where('pay_id', $id)
            ->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id());
            })->with('order')->firstOrFail();

        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $payment = Payment::where('pay_id', $id)
            ->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id());
            })->with('order')->firstOrFail();

        return view('payments.edit', compact('payment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $payment = Payment::where('pay_id', $id)
            ->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id());
            })->with('order')->firstOrFail();

        $validatedData = $request->validate([
            'amount' => 'required|numeric',
            'method' => 'required|string',
            'status' => 'required|string',
        ]);
        $payment->update($validatedData);
        return redirect()->route('payments.index')->with('success', 'Payment updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $payment = Payment::where('payment_id', $id)
         ->where('user_id', Auth::id())
         ->firstOrFail(); // Will throw a ModelNotFoundException if the entry doesn't exist or doesn't belong to the user

     $payment->delete();

     return redirect()->route('payments.index')->with('status', 'Payment deleted successfully!');
    }
}
