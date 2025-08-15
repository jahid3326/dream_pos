@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Edit Student</h1>
    <form action="{{ route('students.update', $student->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('students._form', ['buttonText' => 'Update Student'])
    </form>
</div>
@endsection