<div class="card">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Payment Date</label>
                <input type="date" name="payment_date" class="form-control"
                    value="{{ old('payment_date', ($payment->payment_date ?? now())->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Payment Mode</label>
                <select name="payment_mode" class="form-select" required>
                    <option value="Cash" @selected(old('payment_mode', $payment->payment_mode ?? '') == 'Cash')>Cash</option>
                    <option value="Card" @selected(old('payment_mode', $payment->payment_mode ?? '') == 'Card')>Card</option>
                </select>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Amount Paid</label>
                <input type="number" step="0.01" name="amount" class="form-control"
                    value="{{ old('amount', $payment->amount ?? $sale->due_amount) }}" required>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Note</label>
                <textarea name="note" class="form-control">{{ old('note', $payment->note ?? '') }}</textarea>
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        <a href="{{ route('sales.payments.index', $sale->id) }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
