<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CustomerImport;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{

    public function __construct()
    {
        $this->middleware('action.permission:Customer,read')->only('index');
        $this->middleware('action.permission:Customer,create')->only(['create', 'store']);
        $this->middleware('action.permission:Customer,show')->only('show');
        $this->middleware('action.permission:Customer,update')->only(['edit', 'update']);
        $this->middleware('action.permission:Customer,delete')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Customer::with('user', 'createdBy');

        if ($user->role && $user->role->name !== 'Super Admin') {
            // If the user is NOT a Super Admin, only show customers they created.
            $query->where('created_by', $user->id);
        }

        // Handle the status filter from the URL parameter (existing logic)
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->input('status'));
        }

        $customers = $query->latest()->get();

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['nullable', 'string', 'max:25'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
            // Add other customer-specific validations if needed
        ]);

        DB::transaction(function () use ($request) {
            $customerRole = Role::firstOrCreate(['name' => 'Customer']);

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $customerRole->id,
            ];

            if ($request->hasFile('profile_picture')) {
                $userData['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
            }

            $user = User::create($userData);

            Customer::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'phone_number' => $request->phone_number,
                'tax_number' => $request->tax_number,
                'billing_address' => $request->billing_address,
                'status' => $request->status ?? true,
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function ajaxStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // User fields
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['nullable', 'string', 'max:25'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],

            // Customer fields
            'company_name' => ['nullable', 'string', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'billing_address' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->all()], 422);
        }

        $customer = null;
        try {
            DB::transaction(function () use ($request, &$customer) {
                $customerRole = Role::firstOrCreate(['name' => 'Customer']);

                $userData = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'password' => Hash::make($request->password),
                    'role_id' => $customerRole->id,
                ];

                if ($request->hasFile('profile_picture')) {
                    $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                    $userData['profile_picture'] = $path;
                }

                $user = User::create($userData);

                $customer = Customer::create([
                    'user_id' => $user->id,
                    'company_name' => $request->company_name,
                    'phone_number' => $request->phone_number,
                    'tax_number' => $request->tax_number,
                    'billing_address' => $request->billing_address,
                    'status' => $request->status ?? true,
                    'created_by' => Auth::id(),
                ]);

                $customer->load('user'); // Load the relationship to send back
            });
        } catch (\Exception $e) {
            \Log::error('Customer creation failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An unexpected error occurred.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully!',
            'customer' => $customer
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {

        $this->authorize('view', $customer);
        $customer->load('user');
        // You can pass more related data here if needed
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $this->authorize('update', $customer);

        $customer->load('user');
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);

        $user = $customer->user;
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:25'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
        ]);

        DB::transaction(function () use ($request, $customer, $user) {
            $userData = ['name' => $request->name, 'email' => $request->email];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture) Storage::disk('public')->delete($user->profile_picture);
                $userData['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
            }
            $user->update($userData);

            $customer->update([
                'company_name' => $request->company_name,
                'phone_number' => $request->phone_number,
                'tax_number' => $request->tax_number,
                'billing_address' => $request->billing_address,
                'status' => $request->status ?? true,
            ]);
        });

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function showImportForm()
    {
        return view('customers.import');
    }

    /**
     * Handle the import of customers from a file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'customer_file' => 'required|mimes:xlsx,xls,csv'
        ]);

        // 1. Create a new instance of our import class
        $import = new CustomerImport;

        // 2. Import the file
        Excel::import($import, $request->file('customer_file'));

        // 3. Check for any validation errors collected by the import class
        if (!empty($import->errors)) {
            // If there are errors, redirect back with both a summary and the detailed errors
            return redirect()->route('customers.import.show')
                ->with('import_errors', $import->errors)
                ->with('error', "The import has finished, but with some errors. " . $import->importedCount . " records were imported successfully.");
        }

        // 4. If there were no errors, show a full success message
        return redirect()->route('customers.index')->with('success', 'All customers (' . $import->importedCount . ' records) imported successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $this->authorize('delete', $customer);
        // Deleting the user will cascade and delete the customer record.
        // We just need to handle the profile picture.
        // echo $customer->user->profile_picture;
        // exit;
        if ($customer->user->profile_picture) {
            Storage::disk('public')->delete($customer->user->profile_picture);
        }
        $customer->user->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
