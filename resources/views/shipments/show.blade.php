@extends('layouts.app')
@section('title', 'Shipment Detail - ' . $shipment->shipment_number)

@push('styles')
    <style>
        /* Add any specific styles needed for this page */
        .summary-label {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .summary-value {
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: center;
            border: 1px solid;
        }

        .status-in-process,
        .status-deposit {
            border-color: #ffc107;
            color: #ff8f00;
            background-color: #fff8e1;
        }

        .status-ready,
        .status-complet {
            border-color: #28a745;
            color: #155724;
            background-color: #d4edda;
        }

        .nav-tabs .nav-link {
            color: #6c757d;
        }

        .nav-tabs .nav-link.active {
            color: #0d6efd;
            border-bottom: 2px solid #0d6efd;
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="content">
            @include('layouts._messages')

            {{-- Header Section --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold mb-0">#{{ $shipment->shipment_number }}</h4>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-warning me-3">{{ $shipment->status }}</span>
                        <div class="progress" style="width: 200px; height: 8px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 50%;"></div>
                        </div>
                    </div>
                </div>
                <div>
                    <a href="{{ route('shipments.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>

            {{-- Main Summary Box --}}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        {{-- Left Side: Supplier Statuses & Address --}}
                        <div class="col-lg-4 border-end py-3">
                            <h6 class="text-muted mb-3">Good ready on</h6>
                            @foreach ($shipment->purchase->suppliers as $supplier)
                                <div class="d-flex align-items-center mb-2">
                                    <img src="{{ $supplier->user->profile_picture ? asset('public/storage/' . $supplier->user->profile_picture) : asset('public/storage/images/default_avatar.png') }}"
                                        class="rounded-circle me-2" width="24" height="24"
                                        style="object-fit: cover;">
                                    <span
                                        class="summary-value">{{ $supplier->purchase_details->ready_date ? \Carbon\Carbon::parse($supplier->purchase_details->ready_date)->format('d-m-Y') : 'N/A' }}</span>
                                </div>
                            @endforeach

                            <h6 class="text-muted mt-4 mb-3">Order Status</h6>
                            @foreach ($shipment->purchase->suppliers as $supplier)
                                @php
                                    $readyDate = $supplier->purchase_details->ready_date
                                        ? \Carbon\Carbon::parse($supplier->purchase_details->ready_date)
                                        : null;
                                    $currentDate = \Carbon\Carbon::now();
                                    $status = $readyDate && $readyDate->lte($currentDate) ? 'READY' : 'In Process';
                                    $statusClass = $readyDate && $readyDate->lte($currentDate) ? 'ready' : 'in-process';
                                @endphp
                                <div class="d-flex align-items-center mb-2">
                                    <img src="{{ $supplier->user->profile_picture ? asset('public/storage/' . $supplier->user->profile_picture) : asset('public/storage/images/default_avatar.png') }}"
                                        class="rounded-circle me-2" width="24" height="24"
                                        style="object-fit: cover;">
                                    <span class="status-badge status-{{ $statusClass }}">{{ $status }}</span>
                                </div>
                            @endforeach
                            <address class="mb-0">
                                <p style="padding: 0; margin: 0">Need to ship to:</p>
                                <strong class="d-block">{{ $shipment->customer->user->name }}</strong>
                                {{ $shipment->customer->billing_address }}<br>
                            </address>
                        </div>

                        {{-- Right Side: Financials & Logistics --}}
                        <div class="col-lg-8 ps-lg-4 py-3">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="text-muted">Invoice Number:</div>
                                    <div class="fw-bold summary-value">
                                        {{ $shipment->shipment_number ?? 'N/A' }}</div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="text-muted">Estimation delivery:</div>
                                    <div class="fw-bold summary-value">
                                        {{ $shipment->created_at->addDays(14)->format('d-m-Y H:i a') }}</div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="text-muted">Payment Status:</div>
                                    <div><span
                                            class="status-badge status-{{ Str::slug($paymentStatus) }}">{{ $paymentStatus }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="text-muted">Agent:</div>
                                    <div class="fw-bold summary-value">{{ $shipmentAgent ? $shipmentAgent->name : 'N/A' }}
                                    </div>
                                </div> {{-- Shows assigned shipment agent --}}
                                <div class="col-md-6 mb-4">
                                    <div class="text-muted">Methode shipping:</div>
                                    <div class="fw-bold summary-value">Sea</div>
                                </div> {{-- Placeholder --}}
                                <div class="col-md-6 mb-4">
                                    <div class="text-muted">Cbm Total:</div>
                                    <div class="fw-bold summary-value">{{ number_format($totalCbm, 2) }} cbm</div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="text-muted">Weight:</div>
                                    <div class="fw-bold summary-value">{{ number_format($totalWeight, 0) }} kg</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabs Section --}}
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tracking">Tracking</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#document">Document</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#packing">Information Packing
                                and Supplier</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#payment">Payment</a></li>
                    </ul>
                    <div class="tab-content pt-3">
                        {{-- Tracking Tab --}}
                        <div class="tab-pane fade show active" id="tracking">
                            <form action="{{ route('shipments.addTracking', $shipment) }}" method="POST">@csrf<div
                                    class="input-group mb-3"><input type="url" name="tracking_url" class="form-control"
                                        placeholder="Add tracking URL..." required><button class="btn btn-primary">Add
                                        tracking</button></div>
                            </form>
                            <ul class="list-group">
                                @forelse($trackingUrls as $url)
                                    <li class="list-group-item d-flex justify-content-between align-items-center"><a
                                            href="{{ $url }}" target="_blank">{{ $url }}</a>
                                        <form action="{{ route('shipments.removeTracking', $shipment) }}" method="POST"
                                            class="remove-tracking-form">@csrf @method('DELETE')<input type="hidden"
                                                name="tracking_url" value="{{ $url }}"><button type="submit"
                                                class="btn-close btn-sm"></button></form>
                                </li>@empty<li class="list-group-item text-muted">No tracking URLs added yet.</li>
                                @endforelse
                            </ul>
                        </div>
                        {{-- Document Tab --}}
                        <div class="tab-pane fade" id="document">
                            @include('purchases._documents-panel-content', [
                                'purchase' => $shipment->purchase,
                            ])
                        </div>
                        {{-- Packing Tab --}}
                        <div class="tab-pane fade" id="packing">
                            @foreach ($shipment->purchase->suppliers as $supplier)
                                <div class="d-flex align-items-center p-2 border-bottom">
                                    <a class="btn btn-sm btn-outline-secondary me-2 packing-toggle-btn collapsed"
                                        data-bs-toggle="collapse" href="#packing-{{ $supplier->id }}">
                                        <i class="fas fa-angle-right"></i>
                                    </a>
                                    <img src="{{ $supplier->user->profile_picture ? asset('public/storage/' . $supplier->user->profile_picture) : asset('public/storage/images/default_avatar.png') }}"
                                        class="rounded me-2" width="30" height="30" style="object-fit: contain;">
                                    <h6 class="mb-0">{{ $supplier->user->name }}</h6>
                                </div>
                                <div class="collapse p-3" id="packing-{{ $supplier->id }}">
                                    @if ($cargo = $supplier->purchase_details->cargo)
                                        <div class="row">
                                            <div class="col-lg-8">
                                                <h6>Dimensions <small>(Package or palette)</small></h6>
                                                <div class="row">
                                                    @foreach ($cargo->dimensions as $index => $dim)
                                                        <div class="col-6 mb-1">{{ $index + 1 }} /
                                                            {{ (int) $dim->length }} x {{ (int) $dim->width }} x
                                                            {{ (int) $dim->height }} cm</div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <address class="p-2 border rounded"><strong>Address
                                                        Factory:</strong><br>{{ $supplier->billing_address ?? 'N/A' }}
                                                </address>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-3">
                                                <h6>Total CBM</h6>
                                                <p>{{ $cargo->total_cbm }} cbm</p>
                                            </div>
                                            <div class="col-md-3">
                                                <h6>Gross Weight (kg)</h6>
                                                <p>{{ $cargo->gross_weight }} kg</p>
                                            </div>
                                            <div class="col-md-3">
                                                <h6>Quantity</h6>
                                                <p>{{ $cargo->quantity }} pcs</p>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted">No packing information has been provided by this supplier
                                            yet.</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        {{-- Payment Tab --}}
                        <div class="tab-pane fade" id="payment">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <p class="mb-0 text-muted">Shipping Cost: <span
                                            class="text-dark fw-bold">${{ number_format($shipment->total_amount, 2) }}</span>
                                    </p>
                                    <p class="mb-0 text-muted">Amount Paid: <span
                                            class="text-success fw-bold">${{ number_format($paidAmount, 2) }}</span></p>
                                    <p class="mb-0 text-muted">Amount Due: <span
                                            class="text-danger fw-bold">${{ number_format($dueAmount, 2) }}</span></p>
                                </div>
                                @if (Auth::user() && Auth::user()->isSuperAdmin())
                                    <button class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addShipmentPaymentModal">Add Payment</button>
                                @endif
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Mode</th>
                                            <th>Note</th>
                                            <th>Proof</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($shipment->payments as $payment)
                                            <tr>
                                                <td>{{ $payment->payment_date->format('d M, Y') }}</td>
                                                <td>{{ $payment->payment_mode }}</td>
                                                <td>{{ $payment->note }}</td>
                                                <td>
                                                    @if ($payment->proof)
                                                        <a href="{{ asset('storage/' . $payment->proof) }}"
                                                            target="_blank"><i
                                                            class="far fa-file-pdf text-danger fa-lg"></i></a>@else<span
                                                            class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">${{ number_format($payment->amount, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No payments recorded for
                                                    shipping.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Document Files Modal -->
    <div class="modal fade" id="documentFilesModal" tabindex="-1" aria-labelledby="documentFilesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentFilesModalLabel">Uploaded Files for [Document Name]</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- The list of files will be injected here by JavaScript --}}
                    <ul class="list-group" id="document-file-list">
                        {{-- JS will populate this --}}
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Add Shipment Payment Modal --}}
    @if (Auth::user() && Auth::user()->isSuperAdmin())
        @include('shipments._add-payment-modal', ['shipment' => $shipment, 'dueAmount' => $dueAmount])
    @endif
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // --- FRESH JAVASCRIPT FOR TRACKING & PAYMENTS ---

            // 1. Confirmation for Removing Tracking URL
            $('.remove-tracking-form').on('submit', function(event) {
                event.preventDefault();
                const form = this;
                Swal.fire({
                    title: 'Remove this tracking URL?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, remove it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            @if (Auth::user() && Auth::user()->isSuperAdmin())
                // --- AJAX Submission for the Add Shipment Payment Modal ---
                $('#add-shipment-payment-form').on('submit', function(event) {
                    event.preventDefault();
                    const form = $(this);
                    const url = form.attr('action');
                    const formData = new FormData(this);
                    const submitButton = form.find('button[type="submit"]');
                    const errorContainer = $('#shipment-payment-errors'); // Target the correct error div

                    submitButton.prop('disabled', true).text('Saving...');
                    errorContainer.hide().empty(); // Clear previous errors

                    // Clear previous validation styling
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback').remove();

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            // On success, simply reload the page to show the new payment in the list
                            // and the updated financial summary.
                            location.reload();
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) { // Validation errors
                                const errors = xhr.responseJSON.errors;
                                let errorHtml = '<ul>';
                                $.each(errors, function(key, value) {
                                    errorHtml += `<li>${value[0]}</li>`;
                                    const input = form.find(`[name="${key}"]`);
                                    input.addClass('is-invalid');
                                    input.after(
                                        `<div class="invalid-feedback d-block">${value[0]}</div>`
                                    );
                                });
                                errorHtml += '</ul>';
                                errorContainer.html(errorHtml).show(); // Show validation errors
                            } else {
                                alert('An error occurred. Please try again.');
                            }
                            submitButton.prop('disabled', false).text('Save Payment');
                        }
                    });
                });
            @endif

            // Helper function to get the correct icon class based on a filename
            function getFileIcon(filename) {
                // Get the extension and convert to lower case
                const extension = filename.split('.').pop().toLowerCase();

                let iconClass = 'fa-file'; // Default icon
                let iconColor = 'text-secondary';

                const fileTypes = {
                    'text-danger': ['pdf'],
                    'text-info': ['jpg', 'jpeg', 'png', 'gif', 'svg'],
                    'text-primary': ['doc', 'docx'],
                    'text-success': ['xls', 'xlsx', 'csv'],
                    'text-warning': ['zip', 'rar']
                };

                const iconMap = {
                    'pdf': 'fa-file-pdf',
                    'jpg': 'fa-file-image',
                    'jpeg': 'fa-file-image',
                    'png': 'fa-file-image',
                    'gif': 'fa-file-image',
                    'svg': 'fa-file-image',
                    'doc': 'fa-file-word',
                    'docx': 'fa-file-word',
                    'xls': 'fa-file-excel',
                    'xlsx': 'fa-file-excel',
                    'csv': 'fa-file-excel',
                    'zip': 'fa-file-archive',
                    'rar': 'fa-file-archive',
                };

                if (iconMap[extension]) {
                    iconClass = iconMap[extension];
                    for (const color in fileTypes) {
                        if (fileTypes[color].includes(extension)) {
                            iconColor = color;
                            break;
                        }
                    }
                }

                return `fas ${iconClass} ${iconColor} fa-fw me-2`;
            }

            // Listen for clicks on any element with the 'document-modal-trigger' class
            $('.document-modal-trigger').on('click', function(event) {
                event.preventDefault();

                const button = $(this);
                const documentName = button.data('document-name');
                const files = button.data('files-json');

                const modal = $('#documentFilesModal');
                const fileListContainer = modal.find('#document-file-list');

                // 1. Update the modal's title
                modal.find('#documentFilesModalLabel').text('Uploaded Files for ' + documentName);

                // 2. Clear any old file list items
                fileListContainer.empty();

                // 3. Build and append the new file list
                if (files && files.length > 0) {
                    files.forEach(function(file) {
                        // --- THIS IS THE KEY CHANGE ---
                        // Call our helper function to get the dynamic icon class string
                        const iconClasses = getFileIcon(file.name);

                        const listItemHtml = `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-truncate" title="${file.name}">
                            <i class="${iconClasses}"></i>
                            ${file.name}
                        </span>
                        <a href="${file.url}" target="_blank" class="btn btn-sm btn-outline-primary">
                            Preview
                        </a>
                    </li>`;
                        fileListContainer.append(listItemHtml);
                    });
                } else {
                    fileListContainer.html('<li class="list-group-item text-muted">No files found.</li>');
                }
            });

            const packingCollapseElements = document.querySelectorAll('div.collapse[id^="packing-"]');

            packingCollapseElements.forEach(element => {
                // When a section starts to SHOW
                element.addEventListener('show.bs.collapse', function() {
                    // Find the button that controls this section and change its icon
                    const triggerButton = $(`a.packing-toggle-btn[href="#${this.id}"]`);
                    triggerButton.find('i').removeClass('fa-angle-right').addClass('fa-angle-down');
                });

                // When a section starts to HIDE
                element.addEventListener('hide.bs.collapse', function() {
                    // Find the button that controls this section and change it back
                    const triggerButton = $(`a.packing-toggle-btn[href="#${this.id}"]`);
                    triggerButton.find('i').removeClass('fa-angle-down').addClass('fa-angle-right');
                });
            });
        });
    </script>
@endpush
