<?php

namespace App\Imports;

use App\Models\Supplier;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SupplierImport implements ToModel, WithHeadingRow, WithValidation
{
    private $supplierRole;

    public function __construct()
    {
        // Find the "Supplier" role once and reuse it for efficiency.
        $this->supplierRole = Role::firstOrCreate(['name' => 'Supplier']);
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // 1. Create the User
        $user = User::create([
            'name'     => $row['name'],
            'email'    => $row['email'],
            'password' => Hash::make($row['password']),
            'role_id'  => $this->supplierRole->id,
        ]);

        // 2. Create the Supplier and link it to the User
        return new Supplier([
            'user_id'         => $user->id,
            'company_name'    => $row['company'],
            'phone_number'    => $row['phone_number'],
            'tax_number'      => $row['tax_number'],
            'billing_address' => $row['billing_address'],
            'status'          => $this->getStatusValue($row['status']),
        ]);
    }

    /**
     * Define validation rules for each row.
     * The import will skip rows that fail validation.
     */
    public function rules(): array
    {
        return [
            // User fields
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            
            // Supplier fields
            'company' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:25',
            'tax_number' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string',
            'status' => 'required|string|in:Enable,Disable,Enabled,Disabled', // Allow variations
        ];
    }

    /**
     * Custom validation messages for better user feedback.
     */
    public function customValidationMessages()
    {
        return [
            'email.unique' => 'The email :input has already been taken.',
            'status.in' => 'The status for ":input" is invalid. Please use "Enable" or "Disable".',
        ];
    }
    
    /**
     * Helper function to convert status string to boolean (1 or 0).
     *
     * @param string|null $statusString
     * @return bool
     */
    private function getStatusValue(?string $statusString): bool
    {
        // Make the check case-insensitive and handle variations
        if (in_array(strtolower($statusString), ['enable', 'enabled'])) {
            return true; // 1
        }
        return false; // 0
    }
}
