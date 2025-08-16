@extends('layouts.app')
@section('title', 'Navigation')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Navigations</h4>
                        <h6>Edit your navigation item</h6>
                    </div>
                </div>
            </div>
            <form action="{{ route('admin.nav-items.update', $navItem->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.nav-items._form', ['buttonText' => 'Update Item'])
            </form>
        </div>
    </div>
@endsection
