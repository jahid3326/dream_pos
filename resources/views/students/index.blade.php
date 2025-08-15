@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Students List</h1>

        {{-- Use the new helper function --}}
        @if (hasActionPermission('Student', 'create'))
            <a href="{{ route('students.create') }}" class="btn btn-primary">Add New Student</a>
        @endif
    </div>
    
    @include('layouts._messages')
    
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Roll No.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Class</th>
                        <th>Parent Name</th>
                        
                        {{-- Check permissions once before the loop --}}
                        @php
                            $canUpdate = hasActionPermission('Student', 'update');
                            $canDelete = hasActionPermission('Student', 'delete');
                            $canPrint = hasActionPermission('Student', 'print');
                        @endphp
                        
                        @if ($canUpdate || $canDelete || $canPrint)
                            <th width="150px">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                    <tr>
                        <td>{{ $student->roll_number }}</td>
                        <td>{{ $student->user->name }}</td>
                        <td>{{ $student->user->email }}</td>
                        <td>{{ $student->class_name }}</td>
                        <td>{{ $student->parent_name }}</td>
                        
                        @if ($canUpdate || $canDelete)
                            <td>
                                <div class="d-flex gap-2">
                                    {{-- Edit Button --}}
                                    @if ($canUpdate)
                                        <a class="btn btn-primary btn-sm" href="{{ route('students.edit', $student->id) }}">Edit</a>
                                    @endif

                                    {{-- Delete Button (needs a form) --}}
                                    @if ($canDelete)
                                        <form action="{{ route('students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    @endif

                                    {{-- ADD THE PRINT BUTTON HERE --}}
                                    @if ($canPrint)
                                        {{-- This link can either go to a print-friendly page or trigger JavaScript --}}
                                        <a href="#" onclick="window.print(); return false;" class="btn btn-info">
                                            <i class="fas fa-print"></i> Print
                                        </a>
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                    @empty
                        {{-- Calculate colspan dynamically for the "No records" message --}}
                        @php $colCount = 5 + (($canUpdate || $canDelete) ? 1 : 0); @endphp
                        <tr><td colspan="{{ $colCount }}" class="text-center">No students found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection