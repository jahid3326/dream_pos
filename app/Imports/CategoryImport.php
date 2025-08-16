<?php

namespace App\Imports;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class CategoryImport implements ToCollection, WithHeadingRow
{
    public $errors = [];
    public $importedCount = 0;

    /**
     * This method is called for the entire collection of rows.
     */
    public function collection(Collection $rows)
    {
        // --- First Pass: Create all top-level categories ---
        foreach ($rows as $rowIndex => $row) {
            // Process only rows with an empty parent_category_name
            if (empty($row['parent_category_name'])) {
                $validator = Validator::make($row->toArray(), [
                    'name' => 'required|string|unique:categories,name',
                ]);

                if ($validator->fails()) {
                    $this->errors[] = "Row " . ($rowIndex + 2) . ": " . $validator->errors()->first();
                    continue;
                }

                Category::create(['name' => $row['name']]);
                $this->importedCount++;
            }
        }

        // --- Second Pass: Create all sub-categories ---
        foreach ($rows as $rowIndex => $row) {
            // Process only rows that have a parent_category_name
            if (!empty($row['parent_category_name'])) {
                $validator = Validator::make($row->toArray(), [
                    'name' => 'required|string|unique:categories,name',
                    'parent_category_name' => 'required|string|exists:categories,name', // Now this rule will work
                ], [
                    'parent_category_name.exists' => 'The parent category ":input" was not found in the database or in the import file.',
                ]);

                if ($validator->fails()) {
                    $this->errors[] = "Row " . ($rowIndex + 2) . ": " . $validator->errors()->first();
                    continue;
                }

                // Find the parent that was created in the first pass
                $parentCategory = Category::where('name', $row['parent_category_name'])->first();

                Category::create([
                    'name' => $row['name'],
                    'parent_id' => $parentCategory->id,
                ]);
                $this->importedCount++;
            }
        }
    }
}