<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load the user data to prevent N+1 query issues
        $students = Student::with('user')->latest()->paginate(10);
        return view('students.index', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roll_number' => ['required', 'string', 'unique:students'],
            'class_name' => ['required', 'string', 'max:255'],
        ]);

        // Wrap in a transaction
        DB::transaction(function () use ($request) {
            // Find the "Student" role. Create it if it doesn't exist.
            $studentRole = Role::firstOrCreate(['name' => 'Student']);

            // 1. Create the User record
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $studentRole->id,
            ]);

            // 2. Create the Student record and link it to the user
            Student::create([
                'user_id' => $user->id,
                'roll_number' => $request->roll_number,
                'class_name' => $request->class_name,
                'parent_name' => $request->parent_name,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
            ]);
        });

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        // Load the related user data for the form
        $student->load('user');
        return view('students.edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $user = $student->user;

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'roll_number' => ['required', 'string', 'unique:students,roll_number,' . $student->id],
            'class_name' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request, $student, $user) {
            // 1. Update the User record
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $user->update($userData);

            // 2. Update the Student record
            $student->update([
                'roll_number' => $request->roll_number,
                'class_name' => $request->class_name,
                'parent_name' => $request->parent_name,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
            ]);
        });

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        // The user record will be deleted automatically because of `onDelete('cascade')`
        // but it's good practice to wrap it in a transaction for other potential logic.
        DB::transaction(function () use ($student) {
            $student->delete();
        });

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }
}
