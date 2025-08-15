@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4 class="fw-bold">Navigations</h4>
                    <h6>Create new navigation item</h6>
                </div>
            </div>
        </div>
        <form action="{{ route('admin.nav-items.store') }}" method="POST">
            @csrf
            @include('admin.nav-items._form', ['buttonText' => 'Create Item'])
        </form>
    </div>
</div>
@endsection