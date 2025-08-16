@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Tax</h4>
                        <h6>Edit Tax</h6>
                    </div>
                </div>
            </div>
            <form action="{{ route('taxes.update', $tax->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('taxes._form', ['buttonText' => 'Update Tax'])
            </form>
        </div>
    </div>
@endsection
