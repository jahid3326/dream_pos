@extends('layouts.app')
@section('title', 'Purchases')
@push('styles')
    <style>
        /* Custom styles to match the design */
        .supplier-financials {
            font-size: 0.8rem;
            vertical-align: middle;
        }

        .supplier-financials .avatar-image {
            object-fit: cover;
            border: 1px solid #eee;
        }

        .file-status-list span {
            display: block;
            font-size: 0.8rem;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-align: center;
            border: 1px solid transparent;
        }

        .status-in.process,
        .status-ordered,
        .status-deposit.payment {
            background-color: #ffe8d1;
            color: #ff8f00;
            border-color: #ff8f00;
        }

        .status-waiting.payment {
            background-color: #fff0f0;
            color: #f44336;
            border-color: #f44336;
        }

        .status-waiting.review,
        .status-waiting.review.from.supplier {
            background-color: #e0e0e0;
            color: #424242;
            border-color: #757575;
        }

        .status-complete,
        .status-received,
        .status-paid {
            background-color: #d4edda;
            color: #155724;
            border-color: #155724;
        }

        .status-deposit,
        .status-unpaid {
            background-color: #fff0f0;
            color: #f44336;
            border-color: #f44336;
        }

        .product-toggle-btn i {
            transition: transform 0.2s ease-in-out;
        }

        .product-toggle-btn.collapsed i {
            transform: rotate(0deg);
        }

        .product-toggle-btn:not(.collapsed) i {
            transform: rotate(90deg);
        }
    </style>
@endpush
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Purchase List</h4>
                        <h6>Manage your Purchases</h6>
                    </div>
                </div>
                <div class="page-btn">

                </div>
            </div>

            @include('layouts._messages')
            <div class="card">
                <div class="card-body">
                    {{-- Filter/Search Section --}}
                    <form action="{{ route('purchases.index') }}" method="GET">
                        <div class="row mb-4">
                            <div class="col-md-3"><label class="form-label">Order Number</label><input type="text"
                                    class="form-control" name="search" placeholder="Search by PO #..."
                                    value="{{ request('search') }}"></div>
                            <div class="col-md-3"><label class="form-label">Supplier</label><select class="form-select"
                                    name="supplier_id">
                                    <option value="">All Suppliers</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>
                                            {{ $supplier->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3"><label class="form-label">Status</label><select class="form-select"
                                    name="status">
                                    <option value="">All Statuses</option>
                                    @foreach ($statuses as $key => $value)
                                        <option value="{{ $key }}" @selected(request('status') == $key)>{{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end"><button type="submit"
                                    class="btn btn-primary me-2"><i class="fas fa-search me-1"></i> Filter</button><a
                                    href="{{ route('purchases.index') }}" class="btn btn-secondary"><i
                                        class="fas fa-times me-1"></i> Clear</a></div>
                        </div>
                    </form>

                    {{-- Main Table --}}
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 5%;"></th>
                                    <th>Purchase Number</th>
                                    <th>Purchase Date</th>
                                    <th>Supplier</th>
                                    <th>Purchase Status</th>
                                    <th class="text-end">Total Amount</th>
                                    <th class="text-end">Paid Amount</th>
                                    <th class="text-end">Due Amount</th>
                                    <th>Payment Status</th>
                                    <th class="no-sort">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchases as $purchase)
                                    <tr>
                                        <td><a class="btn btn-sm btn-outline-secondary accordion-toggle"
                                                data-bs-toggle="collapse" href="#purchaseItems{{ $purchase->id }}"><i
                                                    class="fas fa-plus"></i></a></td>
                                        <td>{{ $purchase->purchase_number }}</td>
                                        <td>{{ $purchase->purchase_date->format('d-m-Y') }}</td>
                                        <td>
                                            <div class="d-flex">
                                                @foreach ($purchase->suppliers as $supplier)
                                                    <a href="#" class="avatar-group-item" data-bs-toggle="tooltip"
                                                        title="{{ $supplier->company_name }}"><img
                                                            src="{{ $supplier->user->profile_picture ? asset('public/storage/' . $supplier->user->profile_picture) : asset('public/storage/images/default_avatar.png') }}"
                                                            alt="{{ $supplier->company_name }}"
                                                            class="rounded avatar-image" style="object-fit: contain"
                                                            width="30" height="30"
                                                            style="margin-left: -10px; border: 2px solid white;"></a>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td><span
                                                class="status-badge status-{{ Str::slug($purchase->status) }}">{{ ucfirst($purchase->status) }}</span><small
                                                class="d-block text-muted">{{ $purchase->progress_text }}</small></td>
                                        <td class="text-end fw-bold">${{ number_format($purchase->total_amount, 2) }}</td>
                                        <td class="text-end supplier-financials">
                                            @foreach ($purchase->suppliers as $supplier)
                                                <div class="d-flex justify-content-end align-items-center mb-1"><img
                                                        src="{{ $supplier->user->profile_picture ? asset('public/storage/' . $supplier->user->profile_picture) : asset('public/storage/images/default_avatar.png') }}"
                                                        class="rounded me-1 avatar-image" style="object-fit: contain"
                                                        width="18"
                                                        height="18"><span>${{ number_format($supplier->paid_amount, 2) }}</span>
                                                </div>
                                            @endforeach
                                        </td>
                                        <td class="text-end supplier-financials">
                                            @foreach ($purchase->suppliers as $supplier)
                                                <div class="d-flex justify-content-end align-items-center mb-1"><img
                                                        src="{{ $supplier->user->profile_picture ? asset('public/storage/' . $supplier->user->profile_picture) : asset('public/storage/images/default_avatar.png') }}"
                                                        class="rounded me-1 avatar-image" style="object-fit: contain"
                                                        width="18"
                                                        height="18"><span>${{ number_format($supplier->due_amount, 2) }}</span>
                                                </div>
                                            @endforeach
                                        </td>
                                        <td><span
                                                class="status-badge status-{{ Str::slug($purchase->payment_status) }}">{{ $purchase->payment_status }}</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown"><button class="btn btn-light btn-sm" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false"><i
                                                        class="fas fa-ellipsis-v"></i></button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('purchases.show', $purchase->id) }}"><i
                                                                class="fas fa-eye fa-fw me-2"></i> View</a></li>
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('purchases.edit', $purchase->id) }}"><i
                                                                class="fas fa-edit fa-fw me-2"></i> Edit</a></li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li><a class="dropdown-item view-payments-btn" href="#"
                                                            data-bs-toggle="modal" data-bs-target="#viewPaymentsModal"
                                                            data-url="{{ route('purchases.payments.get', $purchase->id) }}"><i
                                                                class="fas fa-wallet fa-fw me-2"></i> View Payments</a>
                                                    </li>
                                                    <li><a class="dropdown-item add-payment-btn" href="#"
                                                            data-bs-toggle="modal" data-bs-target="#addPaymentModal"
                                                            data-purchase-id="{{ $purchase->id }}"
                                                            data-po-number="{{ $purchase->purchase_number }}"
                                                            data-due-amount="{{ $purchase->due_amount }}"
                                                            data-suppliers='{{ $purchase->suppliers_json }}'><i
                                                                class="fas fa-dollar-sign fa-fw me-2"></i> Add Payment</a>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li><a class="dropdown-item" href="#"><i
                                                                class="fas fa-download fa-fw me-2"></i> Download PO</a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('purchases.destroy', $purchase->id) }}"
                                                            method="POST">@csrf @method('DELETE')<button type="submit"
                                                                class="dropdown-item text-danger delete-button"
                                                                style="margin-left: .4rem"><i
                                                                    class="fas fa-trash fa-fw me-2"></i> Delete</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="collapse-row">
                                        <td colspan="10" class="p-0">
                                            <div class="collapse" id="purchaseItems{{ $purchase->id }}">
                                                <div class="p-3 bg-light border-top">
                                                    <table class="table table-sm bg-white">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th>Supplier</th>
                                                                <th class="text-end">Quantity Products</th>
                                                                <th>Status Review</th>
                                                                <th>Status Production</th>
                                                                <th class="text-end">Total Price</th>
                                                                <th>Files</th>
                                                                <th class="text-end">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($purchase->suppliers as $supplier)
                                                                <tr>
                                                                    <td>
                                                                        <div class="d-flex align-items-center"><a
                                                                                class="btn btn-sm btn-outline-secondary me-2 product-toggle-btn collapsed"
                                                                                data-bs-toggle="collapse"
                                                                                href="#supplierProducts-{{ $purchase->id }}-{{ $supplier->id }}"><i
                                                                                    class="fas fa-angle-right"></i></a><img
                                                                                src="{{ $supplier->user->profile_picture ? asset('public/storage/' . $supplier->user->profile_picture) : asset('public/storage/images/default_avatar.png') }}"
                                                                                class="rounded me-2"
                                                                                style="object-fit: contain" width="30"
                                                                                height="30"><span>{{ $supplier->company_name }}</span>
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-end">{{ $supplier->total_quantity }}
                                                                    </td>
                                                                    <td style="display: flex; flex-direction:column"><span
                                                                            class="status-badge status-{{ Str::slug($supplier->pivot->status_review) }}">{{ ucfirst(str_replace('-', ' ', $supplier->pivot->status_review)) }}</span>
                                                                        {{-- --- THIS IS THE NEW VALIDATION BUTTON --- --}}
                                                                        @if ($supplier->pivot->status_review === 'modification requested')
                                                                            <form
                                                                                action="{{ route('purchases.validateModification', ['purchase' => $purchase, 'supplier' => $supplier]) }}"
                                                                                method="POST" class="d-inline mt-1">
                                                                                @csrf
                                                                                <button type="submit"
                                                                                    class="btn btn-success btn-sm w-100">Validate</button>
                                                                            </form>
                                                                        @endif
                                                                        {{-- --- END OF NEW BUTTON --- --}}
                                                                    </td>
                                                                    <td><span
                                                                            class="status-badge status-{{ Str::slug($supplier->pivot->status_production) }}">{{ ucfirst($supplier->pivot->status_production) }}</span>
                                                                    </td>
                                                                    <td class="text-end">
                                                                        ${{ number_format($supplier->total_price, 2) }}
                                                                    </td>
                                                                    <td class="file-status-list">
                                                                        @foreach ($supplier->file_status_list as $file)
                                                                            <span>{{ $file['name'] }}: <strong
                                                                                    class="fw-bold {{ $file['status'] == 'Ok' ? 'text-success' : 'text-danger' }}">{{ $file['status'] }}</strong></span>
                                                                        @endforeach
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <a href="{{ route('purchases.showSupplierDetails', ['purchase' => $purchase, 'supplier' => $supplier]) }}"
                                                                            class="btn btn-sm btn-outline-secondary"
                                                                            data-bs-toggle="tooltip"
                                                                            title="View Details for {{ $supplier->user->name }}">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                <tr class="collapse-row">
                                                                    <td colspan="6" class="p-0 border-0">
                                                                        <div class="collapse"
                                                                            id="supplierProducts-{{ $purchase->id }}-{{ $supplier->id }}">
                                                                            <div class="p-3"
                                                                                style="background-color: #fdfdfd;">
                                                                                <div class="p-3"
                                                                                    style="background-color: #fdfdfd;">
                                                                                    <table
                                                                                        class="table table-sm table-hover mb-0">
                                                                                        <thead class="thead-light">
                                                                                            <tr>
                                                                                                <th style="width: 5%;">
                                                                                                </th> {{-- Spacer for indent --}}
                                                                                                <th style="width: 35%;">
                                                                                                    Product</th>
                                                                                                <th class="text-end">
                                                                                                    Quantity</th>
                                                                                                <th class="text-end">Unit
                                                                                                    Price</th>
                                                                                                <th class="text-end">Total
                                                                                                    Price</th>
                                                                                                <th class="text-end">CBM
                                                                                                </th>
                                                                                                <th class="text-end">Total
                                                                                                    CBM</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            @php
                                                                                                // Filter the purchase's items to get only those for the current supplier
$supplierItems = $purchase->items->where(
    'supplier_id',
                                                                                                    $supplier->id,
                                                                                                );
                                                                                            @endphp
                                                                                            @foreach ($supplierItems as $item)
                                                                                                @php
                                                                                                    $imageUrl = asset(
                                                                                                        'public/storage/images/default_image.png',
                                                                                                    );
                                                                                                    $categoryName =
                                                                                                        $item->product
                                                                                                            ->category
                                                                                                            ->name ??
                                                                                                        'N/A';
                                                                                                    $measurement =
                                                                                                        'N/A';
                                                                                                    $cbm = 0;

                                                                                                    if (
                                                                                                        $item->variation
                                                                                                    ) {
                                                                                                        // It's a variation
    $measurement =
        $item
            ->variation
            ->measurement;
    $cbm =
        $item
            ->variation
            ->cbm ??
        0;
    $image =
        $item
            ->variation
            ->image ??
        $item
            ->product
            ->product_image;
    $imageUrl = $image
        ? asset(
            'public/storage/' .
                $image,
        )
        : $imageUrl;
} else {
    // It's a single product
                                                                                                        $measurement =
                                                                                                            $item
                                                                                                                ->product
                                                                                                                ->measurement;
                                                                                                        $cbm =
                                                                                                            $item
                                                                                                                ->product
                                                                                                                ->cbm ??
                                                                                                            0;
                                                                                                        $imageUrl = $item
                                                                                                            ->product
                                                                                                            ->product_image
                                                                                                            ? asset(
                                                                                                                'public/storage/' .
                                                                                                                    $item
                                                                                                                        ->product
                                                                                                                        ->product_image,
                                                                                                            )
                                                                                                            : $imageUrl;
                                                                                                    }
                                                                                                    $totalCbm =
                                                                                                        $item->quantity *
                                                                                                        $cbm;
                                                                                                @endphp
                                                                                                <tr>
                                                                                                    <td></td>
                                                                                                    {{-- Spacer --}}
                                                                                                    <td>
                                                                                                        <div
                                                                                                            class="d-flex align-items-center">
                                                                                                            <img src="{{ $imageUrl }}"
                                                                                                                class="rounded me-2"
                                                                                                                width="40"
                                                                                                                height="40"
                                                                                                                style="object-fit: cover;">
                                                                                                            <div>
                                                                                                                <strong>{{ $item->product_name }}</strong><br>
                                                                                                                <small
                                                                                                                    class="text-muted">{{ $categoryName }}
                                                                                                                    ({{ $measurement }})
                                                                                                                </small>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                    <td class="text-end">
                                                                                                        {{ $item->quantity }}
                                                                                                    </td>
                                                                                                    <td class="text-end">
                                                                                                        ${{ number_format($item->unit_price, 2) }}
                                                                                                    </td>
                                                                                                    <td
                                                                                                        class="text-end fw-bold">
                                                                                                        ${{ number_format($item->total_price, 2) }}
                                                                                                    </td>
                                                                                                    <td class="text-end">
                                                                                                        {{ $cbm }}
                                                                                                    </td>
                                                                                                    <td
                                                                                                        class="text-end fw-bold">
                                                                                                        {{ $totalCbm }}
                                                                                                    </td>
                                                                                                </tr>
                                                                                            @endforeach
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No purchases found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $purchases->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Payment Modal -->
    <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPaymentModalLabel">Add Payment for PO #<span
                            id="payment-po-number"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- The form now includes enctype for file uploads --}}
                <form id="add-payment-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        {{-- Container for validation errors from AJAX --}}
                        <div id="payment-errors" class="alert alert-danger" style="display: none;"></div>

                        <div class="row">

                            {{-- NEW: Supplier Selection Dropdown --}}
                            <div class="col-md-12 mb-3">
                                <label for="payment-supplier-select" class="form-label">Payment For Supplier <span
                                        class="text-danger">*</span></label>
                                <select name="supplier_id" id="payment-supplier-select" class="form-select" required>
                                    {{-- Options for this dropdown will be populated dynamically by JavaScript --}}
                                    <option value="" disabled selected>Select a supplier...</option>
                                </select>
                            </div>

                            {{-- Payment Date --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" class="form-control" required>
                            </div>

                            {{-- Payment Mode --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                                <select name="payment_mode" class="form-select" required>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Card">Card</option>
                                    <option value="Cash">Cash</option>
                                </select>
                            </div>

                            {{-- Amount Paid --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Amount Paid <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" id="payment-amount"
                                    class="form-control" required autocomplete="off">
                                <small class="form-text text-muted">Total Amount Due on PO: <span id="due-amount-text"
                                        class="fw-bold"></span></small>
                            </div>

                            {{-- Proof of Payment File Upload --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Proof of Payment (Optional)</label>
                                <input type="file" name="proof" class="form-control">
                            </div>

                            {{-- Payment Note --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Payment Note</label>
                                <textarea name="note" class="form-control" rows="3"></textarea>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary me-1" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Payments Modal -->
    <div class="modal fade" id="viewPaymentsModal" tabindex="-1" aria-labelledby="viewPaymentsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewPaymentsModalLabel">Payment History for PO #<span
                            id="payments-po-number"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Financial Summary will be inserted here by JS --}}
                    <div id="payments-summary-container" class="mb-4"></div>

                    {{-- The table for payments will be dynamically inserted here --}}
                    <div id="payments-list-container">
                        <div class="text-center p-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            // Accordion +/- icon toggle script
            $('.collapse').on('show.bs.collapse', function() {
                $('a.accordion-toggle[href="#' + this.id + '"] i').removeClass('fa-plus').addClass(
                    'fa-minus');
            });
            $('.collapse').on('hide.bs.collapse', function() {
                $('a.accordion-toggle[href="#' + this.id + '"] i').removeClass('fa-minus').addClass(
                    'fa-plus');
            });

            $('.collapse[id^="supplierProducts-"]').on('show.bs.collapse hide.bs.collapse', function(event) {
                // THIS IS THE FIX: Stop the inner accordion's events (show/hide)
                // from bubbling up to the parent accordion container.
                event.stopPropagation();
            });

            // =========================================================================
            // 2. DEFINITIVE FIX FOR DROPDOWN VISIBILITY IN A SCROLLING TABLE
            // =========================================================================
            // We use event delegation on the '.table-responsive' container.

            // Listen for the 'show.bs.dropdown' event, which fires just before a menu is shown
            $('.table-responsive').on('show.bs.dropdown', function(e) {
                // 'e.relatedTarget' is the button that triggered the dropdown. This is the most reliable way.
                var button = $(e.relatedTarget);

                // Find the dropdown menu, which is the next sibling of the button
                var menu = button.next('.dropdown-menu');

                // Get the position of the button relative to the viewport
                var buttonRect = e.relatedTarget.getBoundingClientRect();

                // Move the menu from the table to the main body of the page.
                // This allows it to break free from the table's 'overflow' restriction.
                $('body').append(menu.detach());

                // Manually set the menu's CSS position to be perfectly aligned with the button
                menu.css({
                    'display': 'block',
                    'position': 'absolute',
                    'top': (buttonRect.top + buttonRect.height + window.scrollY) + 'px',
                    'left': 'auto', // Reset left positioning
                    'right': (window.innerWidth - buttonRect.right) + 'px' // Align right edge
                });

                // Store a reference to the original parent (the <div class="dropdown">)
                // so we know where to put the menu back.
                menu.data('original-parent', button.parent());
            });

            // When clicking ANYWHERE on the document, we check if we need to close our detached dropdown
            $(document).on('click', function(e) {
                var detachedMenu = $('body > .dropdown-menu');

                // If a detached menu exists AND the click was NOT on the dropdown button itself...
                // Note: .closest() is used to handle clicks on the icon inside the button.
                if (detachedMenu.length > 0 && $(e.target).closest('[data-bs-toggle="dropdown"]').length ===
                    0) {

                    var originalParent = detachedMenu.data('original-parent');

                    if (originalParent) {
                        // Put the menu back into its original container and hide it.
                        // We also remove the inline styles to prevent future positioning issues.
                        originalParent.append(detachedMenu.detach().hide().css({
                            position: '',
                            top: '',
                            left: '',
                            right: ''
                        }));
                    }
                }
            });

            // Tooltip initializer
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Delete confirmation script
            $('.delete-button').on('click', function(event) {
                event.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // --- ADD PAYMENT MODAL LOGIC ---
            const addPaymentModal = new bootstrap.Modal(document.getElementById('addPaymentModal'));
            const addPaymentForm = $('#add-payment-form');

            // This variable will hold the due amount for the currently selected context (either total PO or specific supplier)
            let currentDueAmountContext = 0;

            // 1. When any "Add Payment" button is clicked, prepare and show the modal.
            $('.add-payment-btn').on('click', function() {
                const button = $(this);
                const purchaseId = button.data('purchase-id');
                const poNumber = button.data('po-number');
                const totalDueOnPO = parseFloat(button.data('due-amount'));

                // Set the initial calculation context to the total PO due amount.
                currentDueAmountContext = totalDueOnPO;

                // Set the form's action URL.
                addPaymentForm.attr('action', `{{ url('purchases') }}/${purchaseId}/payments`);

                // --- Populate Supplier Dropdown ---
                const suppliers = button.data('suppliers');
                const supplierSelect = $('#payment-supplier-select');
                supplierSelect.empty().append(
                    '<option value="" disabled selected>Select a supplier...</option>');
                if (suppliers && Array.isArray(suppliers)) {
                    suppliers.forEach(function(supplier) {
                        supplierSelect.append(
                            `<option value="${supplier.id}" data-due-amount="${supplier.due_amount}">${supplier.name}</option>`
                        );
                    });
                }

                // --- Reset and Populate other modal fields ---
                $('#payment-po-number').text(poNumber);
                addPaymentForm.trigger('reset'); // Clear all fields

                // Initially, set max to total due, show total due text, and leave amount input empty.
                $('#payment-amount').attr('max', totalDueOnPO).val('');
                $('#due-amount-text').text(`Total Due on PO: $${totalDueOnPO.toFixed(2)}`);

                addPaymentForm.find('input[name="payment_date"]').val(new Date().toISOString().split('T')[
                    0]);
                $('#payment-errors').hide().html('');
            });

            // --- 2. When a supplier is selected from the dropdown ---
            $('#payment-supplier-select').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const supplierDueAmount = parseFloat(selectedOption.data('due-amount'));

                if (!isNaN(supplierDueAmount)) {
                    // Update the calculation context to this supplier's specific due amount.
                    currentDueAmountContext = supplierDueAmount;

                    // Update the "Amount Due" text to show THIS supplier's due amount.
                    $('#due-amount-text').text(`Due for this supplier: $${supplierDueAmount.toFixed(2)}`);

                    // Update the 'max' attribute of the amount input.
                    $('#payment-amount').attr('max', supplierDueAmount);

                    // Clear the amount input field, ready for the user to type.
                    $('#payment-amount').val('');
                }
            });

            // --- 3. DYNAMIC DUE AMOUNT CALCULATION (NOW WORKS WITH THE CONTEXT) ---
            $('#payment-amount').on('input', function() {
                const amountBeingPaid = parseFloat($(this).val()) || 0;

                // Calculate the remaining due based on the CURRENT context (either total PO or supplier-specific).
                const remainingDue = currentDueAmountContext - amountBeingPaid;

                // Update the helper text to show the remaining balance.
                $('#due-amount-text').text(`Remaining Due: $${Math.max(0, remainingDue).toFixed(2)}`);
            });

            // 4. Handle the form submission via AJAX (no changes needed here).
            addPaymentForm.on('submit', function(e) {
                e.preventDefault(); // Prevent the default browser form submission.

                const form = $(this);
                const url = form.attr('action');
                const submitButton = form.find('button[type="submit"]');

                // Use FormData to correctly handle file uploads with AJAX.
                const formData = new FormData(this);

                // Disable button and show loading text.
                submitButton.prop('disabled', true).text('Processing...');
                $('#payment-errors').hide(); // Hide old errors on new submission.

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false, // Required for FormData
                    contentType: false, // Required for FormData
                    success: function(response) {
                        if (response.success) {
                            addPaymentModal.hide();

                            // --- Dynamic UI Update ---
                            const purchaseRow = $(
                                `tr[data-purchase-id="${response.purchase_id}"]`);
                            if (purchaseRow.length) {
                                // Update the main row's overall paid and due amounts.
                                purchaseRow.find('.paid-amount-cell').text('$' + response
                                    .new_paid_amount);
                                purchaseRow.find('.due-amount-cell').text('$' + response
                                    .new_due_amount);

                                // Update the payment status badge.
                                const statusCell = purchaseRow.find('.payment-status-cell');
                                const statusSlug = response.new_payment_status.toLowerCase()
                                    .replace(/[\s_]+/g, '-');
                                statusCell.html(
                                    `<span class="status-badge status-${statusSlug}">${response.new_payment_status}</span>`
                                );

                                // Update the 'data-due-amount' on the button for the next time it's clicked.
                                const addPaymentBtn = purchaseRow.find('.add-payment-btn');
                                if (addPaymentBtn.length) {
                                    addPaymentBtn.data('due-amount', parseFloat(response
                                        .new_due_amount.replace(/,/g, '')));
                                }
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Payment Added!',
                                text: response.message,
                                timer: 1500, // Show the message for 1.5 seconds
                                showConfirmButton: false
                            }).then((result) => {
                                // After the alert closes (or the timer runs out), reload the page.
                                location.reload();
                            });

                            // OPTIONAL: If you want to refresh per-supplier financials, you'd need a more complex solution,
                            // but updating the main row is the primary goal and is now complete.
                            // You could trigger a page reload here if needed: location.reload();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) { // Validation error
                            const errors = xhr.responseJSON.errors;
                            const errorContainer = $('#payment-errors');
                            let errorHtml = '<ul>';
                            $.each(errors, (key, value) => {
                                errorHtml += `<li>${value[0]}</li>`;
                            });
                            errorHtml += '</ul>';
                            errorContainer.html(errorHtml).show();
                        } else {
                            alert('An unexpected server error occurred. Please try again.');
                        }
                    },
                    complete: function() {
                        // Re-enable the button after the request is complete.
                        submitButton.prop('disabled', false).text('Submit Payment');
                    }
                });
            });

            // --- VIEW PAYMENTS MODAL ---
            $('.view-payments-btn').on('click', function(e) {
                e.preventDefault();

                const url = $(this).data('url');
                const summaryContainer = $('#payments-summary-container');
                const listContainer = $('#payments-list-container');

                // 1. Reset the modal to its loading state
                summaryContainer.empty();
                listContainer.html(`
                <div class="text-center p-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>`);

                // 2. Make the AJAX call to the controller to fetch payment data
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // a. Update the modal title
                            $('#payments-po-number').text(response.po_number);

                            // b. Build and inject the financial summary
                            const summaryHtml = `
                            <div class="card bg-light border">
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <small class="text-muted d-block">Total Amount</small>
                                            <h5 class="mb-0 fw-bold">$${response.total_amount}</h5>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Amount Paid</small>
                                            <h5 class="mb-0 text-success fw-bold">$${response.paid_amount}</h5>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Amount Due</small>
                                            <h5 class="mb-0 text-danger fw-bold">$${response.due_amount}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                            summaryContainer.html(summaryHtml);

                            // c. Build the payments table HTML
                            let tableHtml = '';
                            if (response.payments && response.payments.length > 0) {
                                tableHtml = `
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Supplier Paid</th>
                                            <th>Payment Mode</th>
                                            <th>Note</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                                response.payments.forEach(function(payment) {
                                    tableHtml += `
                                <tr>
                                    <td>${payment.date}</td>
                                    <td>${payment.supplier_name}</td>
                                    <td>${payment.mode}</td>
                                    <td>${payment.note || ''}</td>
                                    <td class="text-end">$${payment.amount}</td>
                                </tr>`;
                                });

                                tableHtml += `</tbody></table></div>`;
                            } else {
                                tableHtml =
                                    '<p class="text-center text-muted p-4">No payments have been recorded for this purchase order.</p>';
                            }

                            // d. Inject the finished HTML into the modal
                            listContainer.html(tableHtml);
                        }
                    },
                    error: function() {
                        summaryContainer.empty();
                        listContainer.html(
                            '<p class="text-center text-danger p-4">Could not load payment information at this time.</p>'
                        );
                    }
                });
            });

        });
    </script>
@endpush
