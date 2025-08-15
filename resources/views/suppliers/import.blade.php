@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4 class="fw-bold">Supplier</h4>
                    <h6>Bulk Import Suppliers</h6>
                </div>
            </div>
            <div class="page-btn">
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    
        @include('layouts._messages')

        @if (session('import_errors'))
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>The import failed due to the following errors:</strong>
                <ul class="mt-2 mb-0">
                    @foreach (session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="fas fa-xmark"></i></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Upload Excel or CSV File</h5>
                <p>
                    Please ensure your file has a header row with the following column names:
                    <strong>name, email, password, company, phone_number, tax_number, billing_address, status</strong>.
                </p>
                
                <div class="alert alert-info">
                    <strong>Instructions:</strong>
                    <ul>
                        <li>The file must be in .xlsx, .xls, or .csv format.</li>
                        <li>The <strong>email</strong> for each supplier must be unique.</li>
                        <li>The <strong>password</strong> must be at least 8 characters long.</li>
                        <li>The <strong>status</strong> column must contain either "Enable" or "Disable".</li>
                        <li>Rows that fail validation will be skipped automatically.</li>
                    </ul>
                </div>


                <form action="{{ route('suppliers.import.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="supplier_file" class="form-label">Supplier File</label>
                        <input class="form-control @error('supplier_file') is-invalid @enderror" type="file" id="supplier_file" name="supplier_file" required>
                        @error('supplier_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Start Import</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection