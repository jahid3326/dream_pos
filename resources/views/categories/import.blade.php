@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4 class="fw-bold">Categories</h4>
                    <h6>Manage your import categories</h6>
                </div>
            </div>
            <div class="page-btn">
                 <a href="{{ route('categories.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>

        @include('layouts._messages')

        @if (session('import_errors'))
            <div class="alert alert-danger">
                <strong>The import failed due to the following errors:</strong>
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
                    <a href="{{ route('categories.import.sample') }}" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-download me-1"></i> Download Sample File
                    </a>
                </div>
                <p class="mt-2">Please ensure your file has the following columns in the first row (heading): <strong>name, parent_category_name</strong>.</p>
                
                <div class="alert alert-info">
                    <strong>Instructions:</strong>
                    <ul>
                        <li>The file must be in .xlsx or .csv format.</li>
                        <li>The <strong>name</strong> for each category must be unique.</li>
                        <li>For sub-categories, provide the exact name of an existing category in the <strong>parent_category_name</strong> column.</li>
                        <li>Leave <strong>parent_category_name</strong> blank for top-level categories.</li>
                        <li>Rows that fail validation will be skipped automatically.</li>
                    </ul>
                </div>

                <form action="{{ route('categories.import.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="category_file" class="form-label">Category File</label>
                        <input class="form-control @error('category_file') is-invalid @enderror" type="file" id="category_file" name="category_file" required>
                        @error('category_file')
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