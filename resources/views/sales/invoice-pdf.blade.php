<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice #{{ $sale->invoice_number }}</title>
    <style>
        /* A simple, clean style for invoices */
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 28px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .summary-table td {
            padding: 5px 0;
            vertical-align: top;
        }

        .summary-table .label {
            font-weight: bold;
        }

        .summary-table .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .bg-success {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .bg-warning {
            background-color: #fff3cd;
            color: #664d03;
        }

        .bg-danger {
            background-color: #f8d7da;
            color: #842029;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }

        .items-table thead th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .text-end {
            text-align: right;
        }

        .totals-section {
            page-break-inside: avoid;
        }

        /* Prevents the totals from splitting across pages */
        .totals-table {
            float: right;
            width: 45%;
            margin-top: 20px;
        }

        .totals-table td {
            padding: 6px;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-muted {
            color: #6c757d;
        }

        .ps-3 {
            padding-left: 1rem;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .pt-1 {
            padding-top: 0.25rem;
        }

        ul {
            list-style-type: none;
            padding-left: 0;
            margin: 0;
        }

        hr {
            border: 0;
            border-top: 1px solid #eee;
            margin: 20px 0;
        }

        h5 {
            font-size: 14px;
            margin: 15px 0 5px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>INVOICE</h1>
        </div>

        {{-- Sale Information Section --}}
        <table class="summary-table">
            <tr>
                <td style="width: 33%;">
                    <span class="label">Invoice Number:</span><br>
                    {{ $sale->invoice_number }}
                </td>
                <td style="width: 33%;">
                    <span class="label">Customer:</span><br>
                    {{ $sale->customer->user->name }}
                </td>
                <td style="width: 33%;">
                    <span class="label">Order Date:</span><br>
                    {{ $sale->sales_date->format('d-m-Y H:i a') }}
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">Order Status:</span><br>
                    <span class="badge"
                        style="background-color: #e2e3e5; color: #41464b;">{{ ucfirst($sale->order_status) }}</span>
                </td>
                <td>
                    <span class="label">Payment Status:</span><br>
                    @if ($sale->payment_status == 'Paid')
                        <span class="badge bg-success">Paid</span>
                    @elseif($sale->payment_status == 'Partial')
                        <span class="badge bg-warning">Partial</span>
                    @else
                        <span class="badge bg-danger">Unpaid</span>
                    @endif
                </td>
                <td>
                    <span class="label">Order Taken By:</span><br>
                    {{ $sale->user->name }}
                </td>
            </tr>
            <tr style="border-top: 1px solid #eee; margin-top: 10px; padding-top: 10px;">
                <td>
                    <span class="label">Total Amount:</span><br>
                    <span class="fw-bold">${{ number_format($sale->grand_total, 2) }}</span>
                </td>
                <td>
                    <span class="label">Paid Amount:</span><br>
                    ${{ number_format($sale->paid_amount, 2) }}
                </td>
                <td>
                    <span class="label">Due Amount:</span><br>
                    <span class="text-danger fw-bold">${{ number_format($sale->due_amount, 2) }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">Discount:</span><br>
                    ${{ number_format($sale->discount, 2) }}
                </td>
                <td>
                    <span class="label">Shipping:</span><br>
                    ${{ number_format($sale->shipping, 2) }}
                </td>
                <td>
                    <span class="label">Order Tax:</span><br>
                    {{ number_format($sale->orderTax->rate ?? 0, 2) }}%
                </td>
            </tr>
        </table>

        <hr>

        {{-- Item Details Section --}}
        <h5>Order Items</h5>
        @include('sales._sale-items-details-pdf', ['sale' => $sale]) {{-- Re-use the same partial --}}

        <div class="totals-section">
            {{-- Payment Details Section --}}
            @if ($sale->payments->isNotEmpty())
                <h5 style="margin-top: 25px;">Payments</h5>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Payment Mode</th>
                            <th>Note</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('d M, Y') }}</td>
                                <td>{{ $payment->payment_mode }}</td>
                                <td>{{ $payment->note }}</td>
                                <td class="text-end">${{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            {{-- Financial Summary --}}
            <table class="totals-table">
                <tr>
                    <td>Sub Total:</td>
                    <td class="text-end">${{ number_format($sale->sub_total, 2) }}</td>
                </tr>
                @if ($sale->order_tax_amount > 0)
                    <tr>
                        <td>Tax ({{ $sale->orderTax->name ?? '' }} {{ $sale->orderTax->rate ?? 0 }}%):</td>
                        <td class="text-end">${{ number_format($sale->order_tax_amount, 2) }}</td>
                    </tr>
                @endif
                @if ($sale->discount > 0)
                    <tr>
                        <td style="color: #dc3545;">Discount:</td>
                        <td class="text-end" style="color: #dc3545;">-${{ number_format($sale->discount, 2) }}</td>
                    </tr>
                @endif
                @if ($sale->shipping > 0)
                    <tr>
                        <td>Shipping:</td>
                        <td class="text-end">${{ number_format($sale->shipping, 2) }}</td>
                    </tr>
                @endif
                <tr style="border-top: 1px solid #333;">
                    <td class="fw-bold">Grand Total:</td>
                    <td class="text-end fw-bold">${{ number_format($sale->grand_total, 2) }}</td>
                </tr>
                <tr>
                    <td>Paid Amount:</td>
                    <td class="text-end">${{ number_format($sale->paid_amount, 2) }}</td>
                </tr>
                <tr class="fw-bold">
                    <td>Amount Due:</td>
                    <td class="text-end">${{ number_format($sale->due_amount, 2) }}</td>
                </tr>
            </table>
        </div>

    </div>
</body>

</html>
