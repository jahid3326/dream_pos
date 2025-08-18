@extends('layouts.app')
@section('title', 'Categories')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Categories</h4>
                        <h6>Manage your categories</h6>
                    </div>
                </div>
                <div class="page-btn">
                    @if (hasActionPermission('Category', 'create'))
                        <a href="{{ route('categories.create') }}" class="btn btn-primary"><i
                                class="ti ti-circle-plus me-1"></i>Add New Category</a>
                        <a href="{{ route('categories.import.show') }}" class="btn btn-secondary">Import Categories</a>
                    @endif
                </div>
            </div>

            @include('layouts._messages')

            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                    <div class="search-set">
                        <div class="search-input">

                        </div>
                    </div>
                    <div class="d-flex table-dropdown my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                        <div class="d-flex table-dropdown my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                            <div class="dropdown">
                                <a href="javascript:void(0);"
                                    class="dropdown-toggle btn btn-white btn-md d-inline-flex align-items-center"
                                    data-bs-toggle="dropdown">
                                    @if (request('status') === '1')
                                        Actived
                                    @elseif(request('status') === '0')
                                        Inactived
                                    @else
                                        Status
                                    @endif
                                </a>
                                <ul class="dropdown-menu  dropdown-menu-end p-3">
                                    <li>
                                        <a href="{{ route('categories.index', ['status' => '1', 'search' => request('search')]) }}"
                                            class="dropdown-item rounded-1">Active</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('categories.index', ['status' => '0', 'search' => request('search')]) }}"
                                            class="dropdown-item rounded-1">Inactive</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('categories.index', ['search' => request('search')]) }}"
                                            class="dropdown-item rounded-1">All</a>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table tree-table">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 35%;">Name</th>
                                    <th>Category Logo</th>
                                    <th>Created On</th>
                                    <th>Status</th>
                                    @if (hasActionPermission('Category', 'update') || hasActionPermission('Category', 'delete'))
                                        <th class="text-end no-sort"></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categories as $category)
                                    @include('categories._category-list-item', [
                                        'category' => $category,
                                        'level' => 0,
                                    ])
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No categories found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggles = document.querySelectorAll('.toggle-children');

            toggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();

                    const parentId = this.dataset.id;
                    const children = document.querySelectorAll(`.parent-of-${parentId}`);
                    const icon = this.querySelector('i');

                    if (icon.classList.contains('fa-plus-square')) {
                        // Expand
                        children.forEach(child => {
                            child.style.display = 'table-row';
                        });
                        icon.classList.remove('fa-plus-square');
                        icon.classList.add('fa-minus-square');
                        icon.classList.remove('text-primary'); // Optional: change color
                        icon.classList.add('text-danger');
                    } else {
                        // Collapse
                        collapseAllChildren(parentId);
                    }
                });
            });

            function collapseAllChildren(parentId) {
                const children = document.querySelectorAll(`.parent-of-${parentId}`);
                const iconElement = document.querySelector(`.toggle-children[data-id="${parentId}"]`);

                if (iconElement) {
                    const icon = iconElement.querySelector('i');
                    icon.classList.remove('fa-minus-square');
                    icon.classList.add('fa-plus-square');
                    icon.classList.remove('text-danger');
                    icon.classList.add('text-primary');
                }

                children.forEach(child => {
                    child.style.display = 'none';
                    // Recursively get the ID and collapse its children
                    const childId = child.dataset.id;
                    collapseAllChildren(childId);
                });
            }
        });
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
