<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToCollection, WithHeadingRow
{
    private $customerRole;
    public $errors = [];
    public $importedCount = 0;

    public function __construct()
    {
        $this->customerRole = Role::firstOrCreate(['name' => 'Customer']);
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        // Define validation rules once
        $rules = [
            // User fields
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            
            // Customer fields
            'company' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:25',
            'tax_number' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string',
        ];

        // Custom messages that can use the :input placeholder
        $messages = [
            'email.unique' => 'The email :input has already been taken.',
        ];

        // Loop through each row in the collection
        foreach ($rows as $rowIndex => $row) {
            // Create a validator for the current row
            $validator = Validator::make($row->toArray(), $rules, $messages);

            // Check if validation fails
            if ($validator->fails()) {
                // If it fails, collect the errors
                foreach ($validator->errors()->messages() as $attribute => $errorMessages) {
                    $this->errors[] = "Row " . ($rowIndex + 2) . " (Column: {$attribute}): " . $errorMessages[0];
                }
                continue; // Skip to the next row
            }

            // If validation passes, create the records
            $this->createCustomer($row);
            $this->importedCount++;
        }
    }
    
    /**
     * Helper function to create the user and customer.
     */
    private function createCustomer($row)
    {
        $user = User::create([
            'name'     => $row['name'],
            'email'    => $row['email'],
            'password' => Hash::make($row['password']),
            'role_id'  => $this->customerRole->id,
        ]);

        Customer::create([
            'user_id'         => $user->id,
            'company_name'    => $row['company'],
            'phone_number'    => $row['phone_number'],
            'tax_number'      => $row['tax_number'],
            'billing_address' => $row['billing_address'],
            'status'          => $this->getStatusValue($row['status']),
        ]);
    }

    /**
     * Helper function to convert status string to boolean.
     */
    private function getStatusValue(?string $statusString): bool
    {
        if (in_array(strtolower($statusString), ['enable', 'enabled'])) {
            return true;
        }
        return false;
    }
}