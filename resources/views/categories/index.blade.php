@extends('layouts.app')
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
                 @if(hasActionPermission('Category', 'create'))
                    <a href="{{ route('categories.create') }}" class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Add New Category</a>
                @endif
            </div>
        </div>

        @include('layouts._messages')
    
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 40%;">Name</th>
                                <th>Parent Category</th>
                                <th>Category Logo</th>
                                <th class="text-end" style="width: 15%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                {{-- Call the recursive partial for each top-level category --}}
                                @include('categories._category-list-item', ['category' => $category, 'level' => 0])
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center p-4">No categories found.</td>
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
        
        $(document).on('click', '.delete-button', function(){

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
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        })
    </script>
@endpush