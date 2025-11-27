@extends('layouts.app')
@section('title', 'Pack')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Pack</h4>
                        <h6>Create new pack</h6>
                    </div>
                </div>
                <div class="page-btn">
                    <a href="{{ route('packs.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>

            @include('layouts._messages')

            <form action="{{ route('packs.store') }}" method="POST">
                @csrf
                @include('packs._form', ['buttonText' => 'Create Pack'])
            </form>
        </div>
    </div>
@endsection
