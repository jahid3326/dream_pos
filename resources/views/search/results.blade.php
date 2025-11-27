@extends('layouts.app')
@section('title', 'Search Results')

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Search Results</h4>
                    <h6>Results for: "{{ $q }}"</h6>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form action="{{ route('global.search') }}" method="GET" class="row g-2 mb-3">
                        <div class="col-auto">
                            <select name="table" class="form-select">
                                <option value="all" {{ ($table ?? 'all') === 'all' ? 'selected' : '' }}>All</option>
                                <option value="sales" {{ ($table ?? '') === 'sales' ? 'selected' : '' }}>Sales</option>
                                <option value="quotes" {{ ($table ?? '') === 'quotes' ? 'selected' : '' }}>Quotes</option>
                                <option value="purchases" {{ ($table ?? '') === 'purchases' ? 'selected' : '' }}>Purchases
                                </option>
                                <option value="shipments" {{ ($table ?? '') === 'shipments' ? 'selected' : '' }}>Shipments
                                </option>
                            </select>
                        </div>
                        <div class="col">
                            <input type="text" name="q" value="{{ $q }}" class="form-control"
                                placeholder="Search..." autocomplete="off">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary">Search</button>
                        </div>
                    </form>

                    @if (empty($results))
                        <div class="text-muted">No query provided.</div>
                    @else
                        @if (isset($results['sales']))
                            <h5>Sales ({{ $results['sales']->count() }})</h5>
                            <ul class="list-group mb-3">
                                @foreach ($results['sales'] as $item)
                                    <li class="list-group-item">
                                        <a href="{{ route('sales.show', $item) }}">{{ $item->invoice_number }}</a>
                                        <span class="text-muted"> — {{ $item->sales_date?->format('d M Y') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if (isset($results['quotes']))
                            <h5>Quotes ({{ $results['quotes']->count() }})</h5>
                            <ul class="list-group mb-3">
                                @foreach ($results['quotes'] as $item)
                                    <li class="list-group-item">
                                        <a href="{{ route('quotes.show', $item->id) }}">{{ $item->quote_number }}</a>
                                        <span class="text-muted"> — {{ $item->quote_date?->format('d M Y') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if (isset($results['purchases']))
                            <h5>Purchases ({{ $results['purchases']->count() }})</h5>
                            <ul class="list-group mb-3">
                                @foreach ($results['purchases'] as $item)
                                    <li class="list-group-item">
                                        <a href="{{ route('purchases.show', $item) }}">{{ $item->purchase_number }}</a>
                                        <span class="text-muted"> — {{ $item->purchase_date?->format('d M Y') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if (isset($results['shipments']))
                            <h5>Shipments ({{ $results['shipments']->count() }})</h5>
                            <ul class="list-group mb-3">
                                @foreach ($results['shipments'] as $item)
                                    <li class="list-group-item">
                                        <a href="{{ route('shipments.show', $item) }}">{{ $item->shipment_number }}</a>
                                        <span class="text-muted"> — {{ $item->shipment_date?->format('d M Y') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
