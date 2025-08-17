@extends('layouts.app')
@section('title', 'Products')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Products</h4>
                        <h6>Bulk Import Products</h6>
                    </div>
                </div>
                <div class="page-btn">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>

            @include('layouts._messages')

            @if (session('import_errors'))
                <div class="alert alert-danger">
                    <strong>The import had the following errors:</strong>
                    <ul class="mt-2 mb-0">
                        @foreach (session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Upload Excel or CSV File</h5>
                        {{-- Download Sample File Button --}}
                        <a href="{{ route('products.import.sample') }}" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-download me-1"></i> Download Sample File
                        </a>
                    </div>
                    <p>Please use the provided sample file to structure your data. The import process is designed to handle
                        both
                        single and variation products in the same file.</p>

                    <div class="alert alert-info">
                        <h6><strong>Column Guide:</strong></h6>
                        <ul class="mb-0">
                            <li><strong>type:</strong> Use "single" or "variation". Only required for the main product row
                                (parent).</li>
                            <li><strong>parent_sku:</strong> For variation options, enter the SKU of the parent product.
                                Leave
                                blank for main products.</li>
                            <li><strong>name:</strong> The product name. Can be repeated for variations.</li>
                            <li><strong>sku:</strong> A unique code for the product or variation. **This is crucial for
                                linking
                                variations.**</li>
                            <li><strong>supplier, category, tax:</strong> Must match the **names** of existing records in
                                the
                                system exactly.</li>
                            <li><strong>All other fields</strong> (measurement, prices, etc.): Enter details for each
                                specific
                                product or variation. For a variation parent row, these should be left blank.</li>
                        </ul>
                    </div>

                    <h6><strong>How to Structure Your Data:</strong></h6>
                    <p><strong>For a Single Product:</strong></p>
                    <ol>
                        <li>In a new row, set the `type` to "single".</li>
                        <li>Leave `parent_sku` blank.</li>
                        <li>Fill in all other details (sku, name, prices, etc.) on this single row.</li>
                    </ol>

                    <p><strong>For a Variation Product (Multi-step):</strong></p>
                    <ol>
                        <li>
                            <strong>First, create a "Parent" Row:</strong>
                            <ul>
                                <li>Set `type` to "variation".</li>
                                <li>Leave `parent_sku` blank.</li>
                                <li>Provide the main `name`, a unique `sku` for the parent, the `supplier`, and the
                                    `category`.
                                </li>
                                <li>**Important:** Leave all other fields (prices, measurement, etc.) blank for this parent
                                    row.
                                </li>
                            </ul>
                        </li>
                        <li>
                            <strong>Then, add "Child" Rows (one for each option):</strong>
                            <ul>
                                <li>Leave the `type` column blank.</li>
                                <li>In the `parent_sku` column, enter the exact SKU of the parent row you just created.</li>
                                <li>Provide a unique `sku` for this specific variation.</li>
                                <li>Fill in the specific details for this variation (measurement, purchase_price,
                                    sale_price,
                                    etc.).</li>
                            </ul>
                        </li>
                    </ol>

                    <form action="{{ route('products.import.store') }}" method="POST" enctype="multipart/form-data"
                        class="mt-4">
                        @csrf
                        <div class="mb-3">
                            <label for="product_file" class="form-label">Product File (.xlsx, .csv)</label>
                            <input class="form-control" type="file" id="product_file" name="product_file" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Start Import</button>
                    </form>
                </div>
            </div>
        </div>
    @endsection
