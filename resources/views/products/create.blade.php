@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Products</h4>
                        <h6>Add New Product</h6>
                    </div>
                </div>
            </div>
            {{-- ADD THE "ADD NEW TAX" MODAL HERE --}}
            <div class="modal fade" id="addTaxModal" tabindex="-1" aria-labelledby="addTaxModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addTaxModalLabel">Add New Tax</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        {{-- This form will be submitted via AJAX --}}
                        <form id="addTaxForm">
                            <div class="modal-body">
                                {{-- Non-visible CSRF token for AJAX POST request --}}
                                @csrf
                                <div id="tax-errors" class="alert alert-danger" style="display: none;"></div>
                                <div class="mb-3">
                                    <label for="tax_name" class="form-label">Tax Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="tax_name" name="name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="tax_rate" class="form-label">Rate (%) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" id="tax_rate" name="rate" class="form-control" step="0.01"
                                        required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Tax</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('products._form', ['buttonText' => 'Create Product'])
            </form>
        </div>
    </div>
@endsection
