@extends('layouts.app')
@section('title', 'Navigation')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Navigations</h4>
                        <h6>Manage your navigations</h6>
                    </div>
                </div>
                <div class="page-btn">
                    <a href="{{ route('admin.nav-items.create') }}" class="btn btn-primary"><i
                            class="ti ti-circle-plus me-1"></i>Add New Item</a>
                </div>
            </div>

            @include('layouts._messages')

            <div class="card shadow-sm">
                <div class="card-body">
                    <p>Manage the application's sidebar menu structure. Use the 'Order' column to control the display
                        sequence.</p>

                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 10%;">Order</th>
                                <th style="width: 30%;">Name</th>
                                <th>Type</th>
                                <th>Route</th>
                                <th>Icon</th>
                                <th style="width: 15%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($navItems as $item)
                                {{-- 1. Display the Header --}}
                                <tr class="table-secondary font-weight-bold">
                                    <td>{{ $item->order }}</td>
                                    <td colspan="4"><strong>{{ $item->name }}</strong></td>
                                    <td>
                                        <form action="{{ route('admin.nav-items.destroy', $item->id) }}" method="POST"
                                            class="d-flex gap-2">
                                            <a href="{{ route('admin.nav-items.edit', $item->id) }}"
                                                class="btn btn-sm btn-light">Edit Header</a>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="me-2 p-2 d-flex align-items-center border rounded delete-button">
                                                <i data-feather="trash-2" class="feather-trash-2"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- 2. Display Children of the Header (Links or Dropdowns) --}}
                                @if ($item->children->count() > 0)
                                    @foreach ($item->children->sortBy('order') as $child)
                                        {{-- Styling for direct children of the header --}}
                                        <tr class="table-light">
                                            <td>{{ $child->order }}</td>
                                            <td style="padding-left: 30px;">
                                                @if ($child->type === 'dropdown')
                                                    <strong><i class="fas fa-long-arrow-alt-right me-2 text-muted"></i>
                                                        {{ $child->name }}</strong>
                                                @else
                                                    <i class="fas fa-long-arrow-alt-right me-2 text-muted"></i>
                                                    {{ $child->name }}
                                                @endif
                                            </td>
                                            <td><span class="badge bg-info">{{ ucfirst($child->type) }}</span></td>
                                            <td>{{ $child->route ?? 'N/A' }}</td>
                                            <td>{{ $child->icon }}</td>
                                            <td>
                                                <form action="{{ route('admin.nav-items.destroy', $child->id) }}"
                                                    method="POST" class="d-flex gap-2">
                                                    <a href="{{ route('admin.nav-items.edit', $child->id) }}"
                                                        class="me-2 p-2 d-flex align-items-center border rounded">
                                                        <i data-feather="edit" class="feather-edit"></i>
                                                    </a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="me-2 p-2 d-flex align-items-center border rounded delete-button">
                                                        <i data-feather="trash-2" class="feather-trash-2"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>

                                        {{-- 3. If the child is a Dropdown, display its children (Grandchildren) --}}
                                        @if ($child->type === 'dropdown' && $child->children->count() > 0)
                                            @foreach ($child->children->sortBy('order') as $grandchild)
                                                <tr>
                                                    <td>{{ $grandchild->order }}</td>
                                                    <td style="padding-left: 60px;"><i
                                                            class="fas fa-minus me-2 text-muted"></i>
                                                        {{ $grandchild->name }}</td>
                                                    <td><span
                                                            class="badge bg-secondary">{{ ucfirst($grandchild->type) }}</span>
                                                    </td>
                                                    <td>{{ $grandchild->route }}</td>
                                                    <td>{{ $grandchild->icon }}</td>
                                                    <td>
                                                        <form
                                                            action="{{ route('admin.nav-items.destroy', $grandchild->id) }}"
                                                            method="POST" class="d-flex gap-2">
                                                            <a href="{{ route('admin.nav-items.edit', $grandchild->id) }}"
                                                                class="me-2 p-2 d-flex align-items-center border rounded">
                                                                <i data-feather="edit" class="feather-edit"></i>
                                                            </a>
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="me-2 p-2 d-flex align-items-center border rounded delete-button">
                                                                <i data-feather="trash-2" class="feather-trash-2"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No navigation items found. Add a "Header" type
                                        item to begin.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).on('click', '.delete-button', function() {

            // Prevent the form from submitting immediately
            event.preventDefault();

            // Find the closest parent form of the clicked button
            const form = this.closest('form');

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!", // ✅ This works
                cancelButtonText: "Cancel",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-danger ml-1"
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        })
    </script>
@endpush
