@extends('layouts.app')
@section('title', 'Permissions')

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Permissions</h4>
                        <h6>Edit Action Permissions</h6>
                    </div>
                </div>
                <div class="page-btn">
                    <a href="{{ route('admin.action-permissions.index') }}" class="btn btn-secondary">Back to Roles</a>
                </div>
            </div>

            @include('layouts._messages')

            {{-- A card wrapper makes the form stand out visually --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4>Role: <strong>{{ $role->name }}</strong></h4>
                    <p class="mb-0">Manage Create, Read, Update, and Delete permissions for this role.</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.action-permissions.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- These classes style the table --}}
                        <table class="table table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 40%;">Module / Feature</th>
                                    <th class="text-center">Create</th>
                                    <th class="text-center">View List (Read)</th>
                                    <th class="text-center">View Details (Show)</th>
                                    <th class="text-center">Edit / Update</th>
                                    <th class="text-center">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($models as $model)
                                    <tr>
                                        <td><strong>{{ $model }} Management</strong></td>
                                        @php $p = $currentPermissions->get($model); @endphp

                                        <td class="text-center"><input class="form-check-input" type="checkbox"
                                                name="permissions[{{ $model }}][create]" value="1"
                                                {{ $p?->can_create ? 'checked' : '' }}></td>
                                        <td class="text-center"><input class="form-check-input" type="checkbox"
                                                name="permissions[{{ $model }}][read]" value="1"
                                                {{ $p?->can_read ? 'checked' : '' }}></td>

                                        {{-- NEW CHECKBOX CELL FOR SHOW --}}
                                        <td class="text-center"><input class="form-check-input" type="checkbox"
                                                name="permissions[{{ $model }}][show]" value="1"
                                                {{ $p?->can_show ? 'checked' : '' }}></td>

                                        <td class="text-center"><input class="form-check-input" type="checkbox"
                                                name="permissions[{{ $model }}][update]" value="1"
                                                {{ $p?->can_update ? 'checked' : '' }}></td>
                                        <td class="text-center"><input class="form-check-input" type="checkbox"
                                                name="permissions[{{ $model }}][delete]" value="1"
                                                {{ $p?->can_delete ? 'checked' : '' }}></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="card-footer text-end bg-transparent border-top-0 pt-4">
                            <button type="submit" class="btn btn-success">Save Permissions</button>
                            <a href="{{ route('admin.action-permissions.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
