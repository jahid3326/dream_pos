<!-- Add Shipment Payment Modal -->
<div class="modal fade" id="addShipmentPaymentModal" tabindex="-1" aria-labelledby="addShipmentPaymentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addShipmentPaymentModalLabel">Add Payment for Shipment
                    @isset($shipment)
                        #{{ $shipment->shipment_number }}
                    @endisset
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form @isset($shipment)action="{{ route('shipments.addPayment', $shipment) }}"@endisset
                method="POST" enctype="multipart/form-data" id="add-shipment-payment-form">
                @csrf
                <div class="modal-body">
                    {{-- Container for validation errors from AJAX --}}
                    <div id="shipment-payment-errors" class="alert alert-danger" style="display: none;"></div>

                    <div class="mb-3">
                        <label for="shipment-payment-amount" class="form-label">Amount Paid <span
                                class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" id="shipment-payment-amount"
                            class="form-control" placeholder="Enter payment amount" autocomplete="off" required>
                        <small class="form-text">
                            <span class="text-info">Remaining Due: $<span id="remaining-due" class="fw-bold">
                                    @isset($dueAmount)
                                        {{ number_format($dueAmount, 2) }}@else0.00
                                    @endisset
                                </span></span>
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="shipment-payment-date" class="form-label">Payment Date <span
                                class="text-danger">*</span></label>
                        <input type="date" name="payment_date" id="shipment-payment-date" class="form-control"
                            value="{{ now()->format('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="shipment-payment-mode" class="form-label">Payment Mode</label>
                        <select name="payment_mode" id="shipment-payment-mode" class="form-select">
                            <option>Bank Transfer</option>
                            <option>Card</option>
                            <option>Cash</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="shipment-payment-proof" class="form-label">Proof of Payment (Optional)</label>
                        <input type="file" name="proof" id="shipment-payment-proof" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="shipment-payment-note" class="form-label">Note</label>
                        <textarea name="note" id="shipment-payment-note" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
