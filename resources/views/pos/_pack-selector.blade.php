<div class="row">
    <div class="col-md-4">
        <label>Select Pack</label>
        <select id="pack-select" class="form-select">
            <option value="">Choose a Pack...</option>
            @foreach ($packs as $pack)
                <option value="{{ $pack->id }}">{{ $pack->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label>Select Surface</label>
        <select id="surface-select" class="form-select" disabled></select>
    </div>
    <div class="col-md-4">
        <label>Select Option</label>
        <select id="option-select" class="form-select" disabled></select>
    </div>
</div>
