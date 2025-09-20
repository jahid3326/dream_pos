<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Rules\MaxDueAmount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{

    public function __construct()
    {
        $this->middleware('action.permission:SalePayment,read')->only('index');
        $this->middleware('action.permission:SalePayment,create')->only(['create', 'store']);
        $this->middleware('action.permission:SalePayment,show')->only('show');
        $this->middleware('action.permission:SalePayment,update')->only(['edit', 'update']);
        $this->middleware('action.permission:SalePayment,delete')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = SalePayment::with('sale.customer.user')->latest()->get();
        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::with('user')->get();
        // We will fetch invoices via AJAX, so we don't need them here.
        return view('payments.create', compact('customers'));
    }

    /**
     * Fetch unpaid invoices for a customer via AJAX.
     */
    public function getUnpaidInvoices(Customer $customer)
    {
        $sales = Sale::where('customer_id', $customer->id)->get();

        $unpaidSales = $sales->map(function ($sale) {
            $paidAmount = $sale->payments->sum('amount');
            $dueAmount = $sale->grand_total - $paidAmount;

            // Only return invoices that have an amount due
            if ($dueAmount > 0.01) {
                return [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'date' => $sale->sales_date->format('d-m-Y'),
                    'invoice_amount' => $sale->grand_total,
                    'due_amount' => $dueAmount,
                ];
            }
            return null;
        })->filter()->values(); // filter() removes nulls, values() re-indexes the array

        return response()->json($unpaidSales);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'payment_date' => 'required|date',
            'payment_mode' => 'required|string',
            'payments.*.sale_id' => 'required|exists:sales,id',
            'payments' => 'required|array', // Each line item must have an amount
        ]);

        $paymentItems = collect($request->payments)->filter(function ($payment) {
            // Filter out any entries where the amount is 0 or not set
            return isset($payment['amount']) && floatval($payment['amount']) > 0;
        });

        // If, after filtering, the collection is empty, it means no payment was entered.
        if ($paymentItems->isEmpty()) {
            // Redirect back with a specific error message
            return back()->withErrors(['payments' => 'You must enter a payment amount for at least one invoice.'])
                ->withInput();
        }

        // Now, validate each individual payment that was actually entered
        foreach ($paymentItems as $index => $paymentData) {
            $sale = Sale::find($paymentData['sale_id']);
            if (!$sale) continue; // Skip if sale not found

            $paidAmount = $sale->payments->sum('amount');
            $dueAmount = $sale->grand_total - $paidAmount;

            // Manually validate this specific item
            $itemValidator = Validator::make($paymentData, [
                'amount' => 'numeric|min:0.01|max:' . $dueAmount,
            ]);

            if ($itemValidator->fails()) {
                return back()->withErrors($itemValidator->errors()->first())
                    ->withInput();
            }
        }
        // --- END OF VALIDATION LOGIC ---

        DB::transaction(function () use ($request, $paymentItems) {
            // Loop through ONLY the filtered items with an amount > 0
            foreach ($paymentItems as $paymentData) {
                $sale = Sale::find($paymentData['sale_id']);
                if ($sale) {
                    $sale->payments()->create([
                        'amount' => $paymentData['amount'],
                        'payment_date' => $request->payment_date,
                        'payment_mode' => $request->payment_mode,
                        'note' => $request->note,
                    ]);

                    // Update the status of each individual sale
                    $newPaidAmount = $sale->payments()->sum('amount');
                    if (abs($newPaidAmount - $sale->grand_total) < 0.01) {
                        $sale->update(['order_status' => 'delivered']);
                    } else {
                        $sale->update(['order_status' => 'in process']);
                    }
                }
            }
        });

        return redirect()->route('payments.index')->with('success', 'Payment receipt created successfully.');
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
        // Eager load the sale this payment belongs to
        $payment->load('sale.customer.user');
        $sale = $payment->sale;

        // Calculate the maximum amount this payment can be.
        // It's the current due amount PLUS the amount of this specific payment.
        $otherPaymentsAmount = $sale->payments()->where('id', '!=', $payment->id)->sum('amount');
        $sale->max_editable_amount = $sale->grand_total - $otherPaymentsAmount;

        return view('payments.edit', compact('sale', 'payment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalePayment $payment)
    {
        $sale = $payment->sale;
        $otherPaymentsAmount = $sale->payments()->where('id', '!=', $payment->id)->sum('amount');
        $maxAllowed = $sale->grand_total - $otherPaymentsAmount;

        $request->validate([
            'payment_date' => 'required|date',
            'payment_mode' => 'required|string',
            'amount' => ['required', 'numeric', 'gte:0.01', 'max:' . $maxAllowed],
            'note' => 'nullable|string',
        ]);

        // Update the specific payment record
        $payment->update($request->all());

        // Recalculate the sale's overall status after the update
        $newPaidAmount = $sale->fresh()->payments()->sum('amount');
        if (abs($newPaidAmount - $sale->grand_total) < 0.01) {
            $sale->update(['order_status' => 'delivered']);
        } else if ($newPaidAmount > 0) {
            $sale->update(['order_status' => 'in process']);
        } else {
            $sale->update(['order_status' => 'on process']);
        }

        // Redirect back to the main payments list
        return redirect()->route('payments.index')->with('success', 'Payment updated successfully.');
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

        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }
}
