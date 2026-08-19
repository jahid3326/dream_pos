@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
    <style>
        .nav-tabs .nav-link {
            border-radius: .5rem;
        }

        .table thead th {
            background: #f8f9fa;
        }

        .status-pill {
            padding: .25rem .6rem;
            border-radius: .375rem;
            font-size: .82rem;
            font-weight: 600
        }

        .status-delivered {
            background: #28a745;
            color: #fff
        }

        .status-inprocess {
            background: #ff9f1a;
            color: #fff
        }

        .status-waiting {
            background: #ffc107;
            color: #212529
        }

        .payment-paid {
            background: #28a745;
            color: #fff;
            padding: .25rem .6rem;
            border-radius: .375rem
        }

        .payment-waiting {
            background: #ff9f1a;
            color: #fff;
            padding: .25rem .6rem;
            border-radius: .375rem
        }

        .customer-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-block;
            background: #2b6ca3;
            margin-right: .6rem
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Dashboard</h4>
                    <h6>Welcome back, {{ Auth::user()->name }}</h6>
                </div>
            </div>
            {{-- Summary cards --}}
            <div class="mb-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0">Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <div class="card p-3">
                                    <div class="d-flex align-items-center">
                                        <div
                                            style="width:14px;height:14px;border-radius:50%;background:#ff9f1a;margin-right:12px">
                                        </div>
                                        <div>
                                            <div class="text-muted">Purchase</div>
                                            <div class="h4 mb-0">${{ number_format($purchasesTotal ?? 0, 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-2"><a href="{{ route('purchases.index') }}">More infos ›</a></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card p-3">
                                    <div class="d-flex align-items-center">
                                        <div
                                            style="width:14px;height:14px;border-radius:50%;background:#2b6ca3;margin-right:12px">
                                        </div>
                                        <div>
                                            <div class="text-muted">Sales</div>
                                            <div class="h4 mb-0">${{ number_format($salesTotal ?? 0, 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-2"><a href="{{ route('sales.index') }}">More infos ›</a></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card p-3">
                                    <div class="d-flex align-items-center">
                                        <div
                                            style="width:14px;height:14px;border-radius:50%;background:#28a745;margin-right:12px">
                                        </div>
                                        <div>
                                            <div class="text-muted">Client Payment</div>
                                            <div class="h4 mb-0">${{ number_format($clientPaymentsTotal ?? 0, 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-2"><a href="{{ route('payments.index') }}">More infos ›</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top tabs --}}
            <div class="card mb-3">
                <div class="card-body">
                    @php $activeTab = $tab ?? request('tab', 'sales'); @endphp
                    <ul class="nav nav-tabs">
                        <li class="nav-item"><a class="nav-link {{ $activeTab === 'sales' ? 'active' : '' }}"
                                href="?tab=sales">Sales</a></li>
                        <li class="nav-item"><a class="nav-link {{ $activeTab === 'purchase' ? 'active' : '' }}"
                                href="?tab=purchase">Purchase</a></li>
                        <li class="nav-item"><a class="nav-link {{ $activeTab === 'quotation' ? 'active' : '' }}"
                                href="?tab=quotation">Quotation</a></li>
                        <li class="nav-item"><a class="nav-link {{ $activeTab === 'waiting' ? 'active' : '' }}"
                                href="?tab=waiting">Waiting Review Supplier</a></li>
                    </ul>
                </div>
            </div>

            {{-- Table --}}
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                @if (($activeTab ?? 'sales') === 'sales')
                                    <tr>
                                        <th>Invoice Number</th>
                                        <th>Sale Date</th>
                                        <th>Customer</th>
                                        <th>Sales Status</th>
                                        <th>Total Amount</th>
                                        <th>Paid Amount</th>
                                        <th>Due Amount</th>
                                        <th>Payment Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                @elseif(($activeTab ?? '') === 'purchase')
                                    <tr>
                                        <th>PO Number</th>
                                        <th>Purchase Date</th>
                                        <th>Suppliers</th>
                                        <th>Status</th>
                                        <th>Total Amount</th>
                                        <th>Paid</th>
                                        <th>Due</th>
                                        <th>Payment Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                @elseif(($activeTab ?? '') === 'quotation')
                                    <tr>
                                        <th>Quote #</th>
                                        <th>Quote Date</th>
                                        <th>Customer</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Paid</th>
                                        <th>Due</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                @elseif(($activeTab ?? '') === 'waiting')
                                    <tr>
                                        <th>PO Number</th>
                                        <th>Purchase Date</th>
                                        <th>Supplier</th>
                                        <th>Supplier Status</th>
                                        <th>Total</th>
                                        <th>Paid</th>
                                        <th>Due</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                @endif
                            </thead>
                            <tbody>
                                @if (($activeTab ?? 'sales') === 'sales')
                                    @php
                                        $sales =
                                            $sales ??
                                            \App\Models\Sale::with(['customer', 'payments'])
                                                ->latest()
                                                ->take(5)
                                                ->get();
                                    @endphp
                                    @forelse($sales as $sale)
                                        @php
                                            $paid = $sale->payments->sum('amount');
                                            $due = max(0, ($sale->grand_total ?? 0) - $paid);
                                        @endphp
                                        <tr>
                                            <td><a href="{{ route('sales.show', $sale) }}">{{ $sale->invoice_number }}</a>
                                            </td>
                                            <td>{{ $sale->sales_date?->format('d-m-Y') }}</td>
                                            <td>
                                                @php
                                                    $custUser = $sale->customer?->user;
                                                    $custName = $custUser?->name ?? '—';
                                                    $custAvatar = $custUser?->profile_picture ?? null;
                                                    $custInitials = '';
                                                    if ($custName && $custName !== '—') {
                                                        $parts = preg_split('/\s+/', $custName);
                                                        $custInitials = strtoupper(
                                                            substr($parts[0] ?? '', 0, 1) .
                                                                substr($parts[1] ?? '', 0, 1),
                                                        );
                                                    }
                                                @endphp
                                                @if ($custAvatar)
                                                    <img src="{{ asset('storage/' . $custAvatar) }}"
                                                        class="customer-avatar" alt="{{ $custName }}">
                                                @else
                                                    <span class="customer-avatar">{{ $custInitials }}</span>
                                                @endif
                                                {{ $custName }}
                                            </td>
                                            <td>
                                                @php $status = strtolower(str_replace(' ', '', $sale->order_status ?? $sale->status ?? '')) @endphp
                                                @if (str_contains($status, 'deliver') || $status === 'delivered')
                                                    <span class="status-pill status-delivered">Delivered</span>
                                                @elseif(str_contains($status, 'process') || $status === 'onprocess' || $status === 'inprocess')
                                                    <span class="status-pill status-inprocess">In process</span>
                                                @else
                                                    <span
                                                        class="status-pill status-waiting">{{ ucfirst($sale->order_status ?? ($sale->status ?? 'Draft')) }}</span>
                                                @endif
                                            </td>
                                            <td>${{ number_format($sale->grand_total ?? 0, 2) }}</td>
                                            <td>${{ number_format($paid, 2) }}</td>
                                            <td>${{ number_format($due, 2) }}</td>
                                            <td>
                                                @if ($paid >= ($sale->grand_total ?? 0))
                                                    <span class="payment-paid">Paid</span>
                                                @elseif($paid > 0)
                                                    <span class="payment-waiting">Deposit Paid</span>
                                                @else
                                                    <span class="payment-waiting">Waiting payment</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm" type="button"
                                                        data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('sales.show', $sale) }}">View</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">No sales found.</td>
                                        </tr>
                                    @endforelse

                                    @if (isset($sales) && method_exists($sales, 'links'))
                                        <tr>
                                            <td colspan="9" class="p-3">{{ $sales->links() }}</td>
                                        </tr>
                                    @endif
                                @elseif(($activeTab ?? '') === 'purchase')
                                    @php
                                        $purchases =
                                            $purchases ??
                                            \App\Models\Purchase::with(['suppliers.user', 'payments'])
                                                ->latest()
                                                ->take(5)
                                                ->get();
                                    @endphp
                                    @forelse($purchases as $purchase)
                                        @php
                                            $paid = $purchase->payments->sum('amount');
                                            $due = max(0, ($purchase->total_amount ?? 0) - $paid);
                                        @endphp
                                        <tr>
                                            <td><a
                                                    href="{{ route('purchases.show', $purchase) }}">{{ $purchase->purchase_number }}</a>
                                            </td>
                                            <td>{{ $purchase->purchase_date?->format('d-m-Y') }}</td>
                                            <td>
                                                @foreach ($purchase->suppliers as $sup)
                                                    @php
                                                        $supUser = $sup->user ?? null;
                                                        $company = $sup->company_name ?? ($supUser?->name ?? '—');
                                                        $supAvatar = $supUser?->profile_picture ?? null;
                                                        $compInitials = '';
                                                        if ($company && $company !== '—') {
                                                            $parts = preg_split('/\s+/', $company);
                                                            $compInitials = strtoupper(
                                                                substr($parts[0] ?? '', 0, 1) .
                                                                    substr($parts[1] ?? '', 0, 1),
                                                            );
                                                        }
                                                    @endphp
                                                    <div class="d-flex align-items-center mb-1">
                                                        @if ($supAvatar)
                                                            <img src="{{ asset('storage/' . $supAvatar) }}"
                                                                class="customer-avatar me-2" alt="{{ $company }}">
                                                        @else
                                                            <span class="customer-avatar me-2">{{ $compInitials }}</span>
                                                        @endif
                                                        <div>{{ $company }}</div>
                                                    </div>
                                                @endforeach
                                            </td>
                                            <td>
                                                @php
                                                    $pstatus = strtolower(
                                                        str_replace(' ', '', $purchase->status ?? ''),
                                                    );
                                                @endphp
                                                @if (str_contains($pstatus, 'deliver') || $pstatus === 'delivered' || $pstatus === 'received')
                                                    <span
                                                        class="status-pill status-delivered">{{ ucfirst($purchase->status ?? 'Delivered') }}</span>
                                                @elseif(str_contains($pstatus, 'process') || $pstatus === 'ordered' || $pstatus === 'inprocess' || $pstatus === 'onprocess')
                                                    <span
                                                        class="status-pill status-inprocess">{{ ucfirst($purchase->status ?? 'In process') }}</span>
                                                @else
                                                    <span
                                                        class="status-pill status-waiting">{{ ucfirst($purchase->status ?? 'Pending') }}</span>
                                                @endif
                                            </td>
                                            <td>${{ number_format($purchase->total_amount ?? 0, 2) }}</td>
                                            <td>${{ number_format($paid, 2) }}</td>
                                            <td>${{ number_format($due, 2) }}</td>
                                            <td>
                                                @php $total = $purchase->total_amount ?? 0; @endphp
                                                @if ($total > 0 && $paid >= $total)
                                                    <span class="payment-paid">Paid</span>
                                                @elseif($paid > 0)
                                                    <span class="payment-waiting">Deposit</span>
                                                @else
                                                    <span class="payment-waiting">Unpaid</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a class="btn btn-sm btn-light"
                                                    href="{{ route('purchases.show', $purchase) }}">View</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">No purchases found.</td>
                                        </tr>
                                    @endforelse

                                    @if (isset($purchases) && method_exists($purchases, 'links'))
                                        <tr>
                                            <td colspan="9" class="p-3">{{ $purchases->links() }}</td>
                                        </tr>
                                    @endif
                                @elseif(($activeTab ?? '') === 'quotation')
                                    @php
                                        $quotes =
                                            $quotes ??
                                            \App\Models\Quote::with(['customer', 'payments'])
                                                ->latest()
                                                ->take(5)
                                                ->get();
                                    @endphp
                                    @forelse($quotes as $quote)
                                        @php
                                            $paid = $quote->payments->sum('amount');
                                            $due = max(0, ($quote->grand_total ?? 0) - $paid);
                                        @endphp
                                        <tr>
                                            <td><a
                                                    href="{{ route('quotes.show', $quote->id) }}">{{ $quote->quote_number }}</a>
                                            </td>
                                            <td>{{ $quote->quote_date?->format('d-m-Y') }}</td>
                                            @php
                                                $qCustUser = $quote->customer?->user;
                                                $qCustName = $qCustUser?->name ?? '—';
                                                $qCustAvatar = $qCustUser?->profile_picture ?? null;
                                                $qInitials = '';
                                                if ($qCustName && $qCustName !== '—') {
                                                    $parts = preg_split('/\s+/', $qCustName);
                                                    $qInitials = strtoupper(
                                                        substr($parts[0] ?? '', 0, 1) . substr($parts[1] ?? '', 0, 1),
                                                    );
                                                }
                                            @endphp
                                            <td>
                                                @if ($qCustAvatar)
                                                    <img src="{{ asset('storage/' . $qCustAvatar) }}"
                                                        class="customer-avatar" alt="{{ $qCustName }}">
                                                @else
                                                    <span class="customer-avatar">{{ $qInitials }}</span>
                                                @endif
                                                {{ $qCustName }}
                                            </td>
                                            @php
                                                $qstatus = strtolower(str_replace(' ', '', $quote->status ?? ''));
                                            @endphp
                                            <td>
                                                @if (str_contains($qstatus, 'accept') ||
                                                        str_contains($qstatus, 'approve') ||
                                                        $qstatus === 'approved' ||
                                                        $qstatus === 'accepted' ||
                                                        $qstatus === 'completed')
                                                    <span
                                                        class="status-pill status-delivered">{{ ucfirst($quote->status ?? 'Accepted') }}</span>
                                                @elseif(str_contains($qstatus, 'process') || $qstatus === 'inprocess' || $qstatus === 'onprocess')
                                                    <span
                                                        class="status-pill status-inprocess">{{ ucfirst($quote->status ?? 'In process') }}</span>
                                                @else
                                                    <span
                                                        class="status-pill status-waiting">{{ ucfirst($quote->status ?? 'Pending') }}</span>
                                                @endif
                                            </td>
                                            <td>${{ number_format($quote->grand_total ?? 0, 2) }}</td>
                                            <td>${{ number_format($paid, 2) }}</td>
                                            <td>${{ number_format($due, 2) }}</td>
                                            <td class="text-end"><a class="btn btn-sm btn-light" href="#">View</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">No quotations found.</td>
                                        </tr>
                                    @endforelse

                                    @if (isset($quotes) && method_exists($quotes, 'links'))
                                        <tr>
                                            <td colspan="8" class="p-3">{{ $quotes->links() }}</td>
                                        </tr>
                                    @endif
                                @elseif(($activeTab ?? '') === 'waiting')
                                    @php
                                        $waiting =
                                            $waiting ??
                                            \App\Models\Purchase::whereHas('suppliers', function ($q) {
                                                $q->where('purchase_supplier.status_review', '!=', 'complet');
                                            })
                                                ->with(['suppliers.user', 'payments'])
                                                ->latest()
                                                ->take(5)
                                                ->get();
                                    @endphp
                                    @forelse($waiting as $purchase)
                                        @php
                                            $paid = $purchase->payments->sum('amount');
                                            $due = max(0, ($purchase->total_amount ?? 0) - $paid);
                                            // find suppliers with non-complet review
                                            $pendingSuppliers = $purchase->suppliers->filter(
                                                fn($s) => ($s->pivot->status_review ?? '') !== 'complet',
                                            );
                                        @endphp
                                        <tr>
                                            <td><a
                                                    href="{{ route('purchases.show', $purchase) }}">{{ $purchase->purchase_number }}</a>
                                            </td>
                                            <td>{{ $purchase->purchase_date?->format('d-m-Y') }}</td>
                                            <td>
                                                @foreach ($pendingSuppliers as $sup)
                                                    @php
                                                        $supUser = $sup->user ?? null;
                                                        $company = $sup->company_name ?? ($supUser?->name ?? '—');
                                                        $supAvatar = $supUser?->profile_picture ?? null;
                                                        $compInitials = '';
                                                        if ($company && $company !== '—') {
                                                            $parts = preg_split('/\s+/', $company);
                                                            $compInitials = strtoupper(
                                                                substr($parts[0] ?? '', 0, 1) .
                                                                    substr($parts[1] ?? '', 0, 1),
                                                            );
                                                        }
                                                    @endphp
                                                    <div class="d-flex align-items-center mb-1">
                                                        @if ($supAvatar)
                                                            <img src="{{ asset('storage/' . $supAvatar) }}"
                                                                class="customer-avatar me-2" alt="{{ $company }}">
                                                        @else
                                                            <span class="customer-avatar me-2">{{ $compInitials }}</span>
                                                        @endif
                                                        <div>{{ $company }}</div>
                                                    </div>
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($pendingSuppliers as $sup)
                                                    @php $sreview = strtolower(trim($sup->pivot->status_review ?? 'pending')); @endphp
                                                    @if (str_contains($sreview, 'complet') ||
                                                            str_contains($sreview, 'complete') ||
                                                            str_contains($sreview, 'validated') ||
                                                            str_contains($sreview, 'approved'))
                                                        <div class="mb-1"><span
                                                                class="status-pill status-delivered">{{ ucfirst($sup->pivot->status_review ?? 'Completed') }}</span>
                                                        </div>
                                                    @elseif(str_contains($sreview, 'process') ||
                                                            str_contains($sreview, 'inprocess') ||
                                                            str_contains($sreview, 'onprocess') ||
                                                            str_contains($sreview, 'production'))
                                                        <div class="mb-1"><span
                                                                class="status-pill status-inprocess">{{ ucfirst($sup->pivot->status_review ?? 'In process') }}</span>
                                                        </div>
                                                    @else
                                                        <div class="mb-1"><span
                                                                class="status-pill status-waiting">{{ ucfirst($sup->pivot->status_review ?? 'Pending') }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>${{ number_format($purchase->total_amount ?? 0, 2) }}</td>
                                            <td>${{ number_format($paid, 2) }}</td>
                                            <td>${{ number_format($due, 2) }}</td>
                                            <td class="text-end"><a class="btn btn-sm btn-light"
                                                    href="{{ route('purchases.show', $purchase) }}">View</a></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">No pending reviews found.</td>
                                        </tr>
                                    @endforelse

                                    @if (isset($waiting) && method_exists($waiting, 'links'))
                                        <tr>
                                            <td colspan="8" class="p-3">{{ $waiting->links() }}</td>
                                        </tr>
                                    @endif

                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
