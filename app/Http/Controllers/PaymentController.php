<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Rules\MaxDueAmount;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = SalePayment::with('sale.customer.user')->latest()->paginate(20);
        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate(['sale_id' => 'required|exists:sales,id']);
        $sale = Sale::find($request->sale_id);

        $sale->paid_amount = $sale->payments->sum('amount');
        $sale->due_amount = $sale->grand_total - $sale->paid_amount;

        $payment = new SalePayment();
        return view('admin.payments.create', compact('sale', 'payment'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id', // Crucial validation
            'payment_date' => 'required|date',
            'payment_mode' => 'required|string',
            'note' => 'nullable|string',
        ]);

        $sale = Sale::find($request->sale_id);

        $request->validate([
            'amount' => ['required', 'numeric', 'gte:0.01', new MaxDueAmount($sale)],
        ]);

        $sale->payments()->create($request->all());

        // Recalculate and update the sale's status
        $newPaidAmount = $sale->payments()->sum('amount');
        if (abs($newPaidAmount - $sale->grand_total) < 0.01) { // Use tolerance for float comparison
            $sale->update(['order_status' => 'delivered']);
        } else {
            $sale->update(['order_status' => 'partial']);
        }

        return redirect()->route('sales.show', $sale->id)->with('success', 'Payment added successfully.');
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
    public function edit(SalePayment $payment)
    {
        $sale = $payment->sale;
        $otherPayments = $sale->payments()->where('id', '!=', $payment->id)->sum('amount');
        $sale->due_amount = $sale->grand_total - $otherPayments;

        return view('admin.payments.edit', compact('sale', 'payment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalePayment $payment)
    {
        $sale = $payment->sale;
        $otherPayments = $sale->payments()->where('id', '!=', $payment->id)->sum('amount');
        $maxAllowed = $sale->grand_total - $otherPayments;

        $request->validate([
            'payment_date' => 'required|date',
            'payment_mode' => 'required|string',
            'amount' => ['required', 'numeric', 'gte:0.01', 'max:' . $maxAllowed],
            'note' => 'nullable|string',
        ]);

        $payment->update($request->all());

        // Recalculate and update the sale's status
        $newPaidAmount = $sale->payments()->sum('amount');
        if ($newPaidAmount >= $sale->grand_total) {
            $sale->update(['order_status' => 'delivered']);
        } else {
            $sale->update(['order_status' => 'partial']);
        }

        return redirect()->route('sales.show', $sale->id)->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalePayment $payment)
    {
        $sale = $payment->sale;
        $payment->delete();

        // Recalculate and update the sale's status
        $newPaidAmount = $sale->payments()->sum('amount');
        if ($newPaidAmount > 0) {
            $sale->update(['order_status' => 'in process']);
        } else {
            $sale->update(['order_status' => 'on process']);
        }

        return redirect()->route('sales.show', $sale->id)->with('success', 'Payment deleted successfully.');
    }
}
