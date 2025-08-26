@extends('layouts.app')
@section('title', 'Pack')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Pack</h4>
                        <h6>Edit Pack: {{ $pack->name }}</h6>
                    </div>
                </div>
                <div class="page-btn">
                    @if (hasActionPermission('Pack', 'read'))
                        <a href="{{ route('packs.index') }}" class="btn btn-secondary">Back to List</a>
                    @endif
                </div>
            </div>
            @include('layouts._messages')
            <form action="{{ route('packs.update', $pack->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('packs._form', ['buttonText' => 'Update Pack'])
            </form>
        </div>
    </div>
@endsection
