@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Payments</h4>
                    <h6>Create New Payment</h6>
                </div>
                {{-- Button to go back to the main sales list --}}
                <div class="page-btn">
                    <a href="{{ route('payments.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>


            @include('layouts._messages')
            <form action="{{ route('payments.store') }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label>User <span class="text-danger">*</span></label>
                                {{-- Select2 customer dropdown remains the same --}}
                                <select name="customer_id" id="customer-select" class="form-select" required>
                                    <option value="">Select a Customer</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Payment Date <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="payment_date" class="form-control"
                                    value="{{ now()->format('Y-m-d\TH:i') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Payment Mode <span class="text-danger">*</span></label>
                                <select name="payment_mode" class="form-select" required>
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label>Notes</label>
                                <textarea name="note" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h5>Settle below invoices using this payment</h5>
                        <div class="text-end mb-2">
                            <h5>Total Amount to Apply: <strong id="total-applied-display"
                                    class="text-success">$0.00</strong></h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Date</th>
                                        <th>Invoice Amount</th>
                                        <th style="width: 25%;">Action (Amount to Pay)</th>
                                    </tr>
                                </thead>
                                <tbody id="invoices-table-body">
                                    {{-- Rows will be populated by AJAX --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-3 mb-5 text-end">
                    <button type="submit" class="btn btn-primary">Create Payment(s)</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const customerSelect = $('#customer-select');
            const invoicesTbody = $('#invoices-table-body');
            const totalAppliedDisplay = $('#total-applied-display');

            // Initialize Select2
            customerSelect.select2({
                placeholder: "Select or search for a customer...",
                width: '100%'
            });

            // --- Fetch Invoices when a Customer is selected ---
            customerSelect.on('change', function() {
                const customerId = $(this).val();
                invoicesTbody.html('<tr><td colspan="4" class="text-center">Loading invoices...</td></tr>');

                // Reset the total when changing customers
                calculateAppliedTotal();

                if (!customerId) {
                    invoicesTbody.empty();
                    return;
                }

                $.ajax({
                    url: `/customers/${customerId}/unpaid-invoices`,
                    type: 'GET',
                    success: function(invoices) {
                        invoicesTbody.empty();
                        if (invoices.length > 0) {
                            invoices.forEach(function(invoice, index) {
                                const rowHtml = `
                            <tr>
                                <td>${invoice.invoice_number}</td>
                                <td>${invoice.date}</td>
                                <td>
                                    $${parseFloat(invoice.invoice_amount).toFixed(2)}<br>
                                    <small class="text-danger">(Pending: $${parseFloat(invoice.due_amount).toFixed(2)})</small>
                                </td>
                                <td>
                                    <input type="hidden" name="payments[${index}][sale_id]" value="${invoice.id}">
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="payments[${index}][amount]" class="form-control payment-input" 
                                               data-due="${invoice.due_amount}" value="0.00" min="0" max="${invoice.due_amount}">
                                    </div>
                                </td>
                            </tr>`;
                                invoicesTbody.append(rowHtml);
                            });
                        } else {
                            invoicesTbody.html(
                                '<tr><td colspan="4" class="text-center">No unpaid invoices found for this customer.</td></tr>'
                            );
                        }
                    }
                });
            });

            invoicesTbody.on('input', '.payment-input', function() {
                // When any amount is changed, recalculate the total applied
                calculateAppliedTotal();
            });

            /**
             * Calculates the sum of all payment inputs and updates the display.
             */
            function calculateAppliedTotal() {
                let totalApplied = 0;

                // Loop through each visible payment input and sum their values
                invoicesTbody.find('.payment-input').each(function() {
                    totalApplied += parseFloat($(this).val()) || 0;
                });

                // Update the display text
                totalAppliedDisplay.text(`$${totalApplied.toFixed(2)}`);
            }
        });
    </script>
@endpush
