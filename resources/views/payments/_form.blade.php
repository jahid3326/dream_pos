<div class="card shadow-sm">
    <div class="card-body">
        {{-- The hidden sale_id is only needed for the 'create' action --}}
        @if (!isset($payment->id))
            <input type="hidden" name="sale_id" value="{{ $sale->id }}">
        @endif

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                <input type="date" id="payment_date" name="payment_date" class="form-control"
                    value="{{ old('payment_date', ($payment->payment_date ?? now())->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="payment_mode" class="form-label">Payment Mode <span class="text-danger">*</span></label>
                <select id="payment_mode" name="payment_mode" class="form-select" required>
                    <option value="Cash" @selected(old('payment_mode', $payment->payment_mode ?? '') == 'Cash')>Cash</option>
                    <option value="Card" @selected(old('payment_mode', $payment->payment_mode ?? '') == 'Card')>Card</option>
                    <option value="Bank Transfer" @selected(old('payment_mode', $payment->payment_mode ?? '') == 'Bank Transfer')>Bank Transfer</option>
                </select>
            </div>
            <div class="col-md-12 mb-3">
                <label for="amount" class="form-label">Amount Paid <span class="text-danger">*</span></label>
                <input type="number" step="0.01" id="amount" name="amount" class="form-control"
                    value="{{ old('amount', $payment->amount ?? number_format($sale->due_amount, 2, '.', '')) }}"
                    required autocomplete="off">
            </div>
            <div class="col-md-12 mb-3">
                <label for="note" class="form-label">Note</label>
                <textarea id="note" name="note" class="form-control" rows="4">{{ old('note', $payment->note ?? '') }}</textarea>
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        {{-- The cancel button should link back to the main payments list --}}
        <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
