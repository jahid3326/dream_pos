@extends('layouts.app')
@section('title', 'Pack')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Pack</h4>
                        <h6>Pack Details: {{ $pack->name }}</h6>
                    </div>
                </div>
                <div class="page-btn">
                    @if (hasActionPermission('Pack', 'read'))
                        <a href="{{ route('packs.index') }}" class="btn btn-secondary">Back to List</a>
                    @endif
                    @if (hasActionPermission('Pack', 'update'))
                        <a href="{{ route('packs.edit', $pack->id) }}" class="btn btn-primary">Edit Pack</a>
                    @endif
                </div>
            </div>
            @include('layouts._messages')
            <div class="card">
                <div class="card-body">
                    @foreach ($pack->groups as $group)
                        <div class="mb-4">
                            <h5>Group: {{ $group->surface }}</h5>
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Option</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($group->options as $option)
                                        <tr>
                                            <td>{{ $option->option }}</td>
                                            <td>${{ number_format($option->price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
