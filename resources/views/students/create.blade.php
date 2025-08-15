@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Add New Student</h1>
    <form action="{{ route('students.store') }}" method="POST">
        @csrf
        @include('students._form', ['buttonText' => 'Create Student'])
    </form>
</div>
@endsection