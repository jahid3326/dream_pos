@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Pack Details: {{ $pack->name }}</h1>
            <a href="{{ route('packs.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
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
@endsection
