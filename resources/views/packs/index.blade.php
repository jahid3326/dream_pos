@extends('layouts.app')
@section('title', 'Pack')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Pack</h4>
                        <h6>Manage your packs</h6>
                    </div>
                </div>
                <div class="page-btn">
                    @if (hasActionPermission('Pack', 'create'))
                        <a href="{{ route('packs.create') }}" class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Add
                            New Pack</a>
                    @endif
                </div>
            </div>
            @include('layouts._messages')

            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>No. of Groups</th>
                                <th>Created At</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($packs as $pack)
                                <tr>
                                    <td>{{ $pack->name }}</td>
                                    <td>{{ $pack->groups->count() }}</td>
                                    <td>{{ $pack->created_at->format('d M, Y') }}</td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('packs.show', $pack->id) }}"
                                                class="btn btn-sm btn-info">View</a>
                                            <a href="{{ route('packs.edit', $pack->id) }}"
                                                class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('packs.destroy', $pack->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-danger delete-button">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No packs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $packs->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
