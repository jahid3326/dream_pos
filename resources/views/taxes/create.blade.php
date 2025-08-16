@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Taxes</h4>
                        <h6>Add New Tax</h6>
                    </div>
                </div>
            </div>
            <form action="{{ route('taxes.store') }}" method="POST">
                @csrf
                @include('taxes._form', ['buttonText' => 'Create Tax'])
            </form>
        </div>
    </div>
@endsection
