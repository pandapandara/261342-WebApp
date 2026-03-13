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
        $validatedData['payment_date'] = now();
        $validatedData['user_id'] = Auth::id();
        $validatedData['status'] = 'pending'; // Default status is pending

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
                         ->firstOrFail();
        $payment->delete();

        return redirect()->route('payments.index')->with('status', 'Payment deleted successfully!');
    }

    /**
     * Mark payment as complete
     */
    public function markComplete(string $id)
    {
        $payment = Payment::where('payment_id', $id)
                         ->where('user_id', Auth::id())
                         ->firstOrFail();

        $payment->markAsComplete();

        // Mark order as complete when payment is completed
        $payment->order->markAsComplete();

        return redirect()->route('payments.index')->with('success', 'Payment marked as complete.');
    }

    /**
     * Mark payment as failed
     */
    public function markFailed(string $id)
    {
        $payment = Payment::where('payment_id', $id)
                         ->where('user_id', Auth::id())
                         ->firstOrFail();

        $payment->markAsFailed();

        // Mark order as failed when payment fails
        $payment->order->markAsFailed();

        return redirect()->route('payments.index')->with('warning', 'Payment marked as failed.');
    }
}
