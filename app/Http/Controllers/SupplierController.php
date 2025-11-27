<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SupplierImport;
use Maatwebsite\Excel\Validators\ValidationException;

class SupplierController extends Controller
{

    public function __construct()
    {
        $this->middleware('action.permission:Supplier,read')->only('index');
        $this->middleware('action.permission:Supplier,create')->only(['create', 'store']);
        $this->middleware('action.permission:Supplier,show')->only('show');
        $this->middleware('action.permission:Supplier,update')->only(['edit', 'update']);
        $this->middleware('action.permission:Supplier,delete')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Supplier::with('user');

        // Handle the status filter from the URL parameter
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->input('status'));
        }

        $suppliers = $query->latest()->get();

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('suppliers.create');
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
            // Add other supplier-specific validations if needed
        ]);
        
        DB::transaction(function () use ($request) {
            $supplierRole = Role::firstOrCreate(['name' => 'Supplier']);
            
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $supplierRole->id,
            ];

            if ($request->hasFile('profile_picture')) {
                $userData['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
            }

            $user = User::create($userData);

            Supplier::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'phone_number' => $request->phone_number,
                'tax_number' => $request->tax_number,
                'billing_address' => $request->billing_address,
                'status' => $request->status ?? true,
            ]);
        });
        
        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        $supplier->load('user');
        // You can pass more related data here if needed
        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        $supplier->load('user');
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $user = $supplier->user;
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:25'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
        ]);

        DB::transaction(function () use ($request, $supplier, $user) {
            $userData = [ 'name' => $request->name, 'email' => $request->email ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture) Storage::disk('public')->delete($user->profile_picture);
                $userData['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
            }
            $user->update($userData);

            $supplier->update([
                'company_name' => $request->company_name,
                'phone_number' => $request->phone_number,
                'tax_number' => $request->tax_number,
                'billing_address' => $request->billing_address,
                'status' => $request->status ?? true,
            ]);
        });

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function showImportForm()
    {
        return view('suppliers.import');
    }

    /**
     * Handle the import of suppliers from a file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'supplier_file' => 'required|mimes:xlsx,xls,csv'
        ]);

        // 1. Create a new instance of our import class
        $import = new SupplierImport;

        // 2. Import the file
        Excel::import($import, $request->file('supplier_file'));

        // 3. Check for any validation errors collected by the import class
        if (!empty($import->errors)) {
            // If there are errors, redirect back with both a summary and the detailed errors
            return redirect()->route('suppliers.import.show')
                            ->with('import_errors', $import->errors)
                            ->with('error', "The import has finished, but with some errors. " . $import->importedCount . " records were imported successfully.");
        }

        // 4. If there were no errors, show a full success message
        return redirect()->route('suppliers.index')->with('success', 'All suppliers (' . $import->importedCount . ' records) imported successfully!');
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        // Deleting the user will cascade and delete the supplier record.
        // We just need to handle the profile picture.
        // echo $supplier->user->profile_picture;
        // exit;
        if ($supplier->user->profile_picture) {
            Storage::disk('public')->delete($supplier->user->profile_picture);
        }
        $supplier->user->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}
