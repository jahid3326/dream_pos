<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Purchase Order #{{ $purchase->purchase_number }}</title>
    <style>
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

        .fw-bold {
            font-weight: bold;
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
            <h1>PURCHASE ORDER</h1>
        </div>

        {{-- PO Information Section --}}
        <table class="summary-table">
            <tr>
                <td style="width: 33%;"><span class="label">Order Number:</span><br>{{ $purchase->purchase_number }}</td>
                <td style="width: 33%;"><span class="label">PO
                        Date:</span><br>{{ $purchase->purchase_date->format('d-m-Y') }}</td>
                <td style="width: 33%;"><span class="label">Status:</span><br>{{ ucfirst($purchase->status) }}</td>
            </tr>
            @if ($purchase->sale)
                <tr>
                    <td colspan="3"><span class="label">Original Sale
                            Ref:</span><br>{{ $purchase->sale->invoice_number }}</td>
                </tr>
            @endif
        </table>

        {{-- Items & Suppliers Section --}}
        @foreach ($itemsBySupplier as $supplierId => $items)
            @php
                $supplier = $purchase->suppliers->firstWhere('id', $supplierId);
            @endphp
            <div style="margin-bottom: 20px; page-break-inside: avoid;">
                <h5><img src="{{ $supplier->user->profile_picture ? asset('storage/' . $supplier->user->profile_picture) : asset('storage/images/default_avatar.png') }}"
                        class="rounded-circle me-2" width="30" height="30"> {{ $supplier->company_name }}</h5>
                {{-- Reuse the partial you already built --}}
                @include('purchases._purchase-items-details-pdf', ['items' => $items])
            </div>
        @endforeach

        <hr>

        <div style="page-break-inside: avoid;">
            {{-- Required Documents Section --}}
            @if ($purchase->documents->where('is_required', true)->isNotEmpty())
                <h5>Required Documents</h5>
                <ul style="list-style-type: none; padding-left: 0;">
                    @foreach ($purchase->documents->where('is_required', true) as $doc)
                        <li style="margin-bottom: 5px;">
                            - {{ $doc->document_name }}
                            <span style="font-size: 10px; color: #6c757d;">(Status: {{ ucfirst($doc->status) }})</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            {{-- Financial Summary & Payments Section --}}
            <table style="width: 100%; margin-top: 20px;">
                <tr>
                    {{-- Left side: Payment History --}}
                    <td style="width: 55%; vertical-align: top;">
                        @if ($purchase->payments->isNotEmpty())
                            <h5>Payment History</h5>
                            <table class="items-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Supplier</th>
                                        <th>Mode</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchase->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_date->format('d M, Y') }}</td>
                                            <td>
                                                {{-- Safely access the supplier's name. Show 'N/A' if not linked. --}}
                                                {{ $payment->supplier->company_name ?? 'N/A' }}
                                            </td>
                                            <td>{{ $payment->payment_mode }}</td>
                                            <td class="text-end">${{ number_format($payment->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </td>

                    {{-- Right side: Totals --}}
                    <td style="width: 45%; vertical-align: bottom;">
                        <table style="width: 100%; font-size: 13px;">
                            <tr>
                                <td style="padding: 5px;">Total Amount:</td>
                                <td class="text-end" style="padding: 5px;">
                                    ${{ number_format($purchase->total_amount, 2) }}</td>
                            </tr>
                            <tr style="color: #155724;">
                                <td style="padding: 5px;">Amount Paid:</td>
                                <td class="text-end" style="padding: 5px;">
                                    ${{ number_format($purchase->paid_amount, 2) }}</td>
                            </tr>
                            <tr class="fw-bold" style="border-top: 1px solid #333; color: #dc3545;">
                                <td style="padding: 8px 5px;">Amount Due:</td>
                                <td class="text-end" style="padding: 8px 5px;">
                                    ${{ number_format($purchase->due_amount, 2) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

    </div>

    {{-- Script for Print-only View --}}
    @if (isset($print) && $print)
        <script type="text/javascript">
            window.onload = function() {
                window.print();
                setTimeout(function() {
                    window.close();
                }, 1);
            }
        </script>
    @endif
</body>

</html>
