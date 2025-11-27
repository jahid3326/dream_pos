<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Tax;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToCollection, WithHeadingRow
{
    /**
     * Stores validation errors found during the import process.
     * @var array
     */
    public $errors = [];

    /**
     * Counts the number of parent products successfully imported.
     * @var int
     */
    public $importedCount = 0;

    /**
     * Caches related models to reduce database queries.
     * Maps names to IDs for quick lookups.
     */
    private $categories;
    private $suppliers;
    private $taxes;

    public function __construct()
    {
        // Pre-load and cache related data to optimize the import process.
        $this->categories = Category::pluck('id', 'name');
        $this->taxes = Tax::pluck('id', 'name');

        // Correctly map the supplier's company_name to its ID.
        $this->suppliers = Supplier::all()->mapWithKeys(function ($supplier) {
            return [$supplier->company_name => $supplier->id];
        });
    }

    /**
     * This method processes the entire collection of rows from the spreadsheet.
     * It uses a two-pass approach to handle parents and their child variations.
     *
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        // This array will hold parent products created in the first pass, keyed by their SKU.
        $parentProducts = [];

        // --- FIRST PASS: Create all parent products (type 'single' and 'variation') ---
        foreach ($rows as $rowIndex => $row) {
            // We identify parent rows as those that DO NOT have a 'parent_sku'.
            if (empty($row['parent_sku'])) {
                $validator = Validator::make($row->toArray(), [
                    'name' => 'required|string|max:255',
                    'type' => 'required|in:single,variation',
                    'supplier' => 'required|exists:suppliers,company_name',
                    'category' => 'required|exists:categories,name',
                    'sku' => 'required_if:type,single|nullable|string|unique:products,sku|unique:product_variations,sku',
                    'purchase_price' => 'required_if:type,single|nullable|numeric|min:0',
                    'sale_price' => 'required_if:type,single|nullable|numeric|min:0',
                ]);

                if ($validator->fails()) {
                    $this->addError($rowIndex, $validator->errors()->first());
                    continue; // Skip this invalid row
                }

                $product = Product::create([
                    'name' => $row['name'],
                    'type' => strtolower($row['type']),
                    'supplier_id' => $this->suppliers[$row['supplier']] ?? null,
                    'category_id' => $this->categories[$row['category']] ?? null,
                    // These fields are only populated for 'single' type products
                    'sku' => $row['sku'] ?? null,
                    'measurement' => $row['measurement'] ?? null,
                    'cbm' => $row['cbm'] ?? null,
                    'weight' => $row['weight'] ?? null,
                    'purchase_price' => $row['purchase_price'] ?? null,
                    'margin' => $row['margin'] ?? null,
                    'sale_price' => $row['sale_price'] ?? null,
                    'tax_id' => $this->taxes[$row['tax']] ?? null,
                ]);

                // If this is a variation parent, store it for the second pass
                if ($product->type === 'variation') {
                    // Use the parent's SKU from the spreadsheet as the key for easy lookup
                    $parentProducts[$row['sku']] = $product;
                }
                $this->importedCount++;
            }
        }

        // --- SECOND PASS: Create all product variations (children) ---
        foreach ($rows as $rowIndex => $row) {
            // We identify child rows as those that DO have a 'parent_sku'.
            if (!empty($row['parent_sku'])) {
                $validator = Validator::make($row->toArray(), [
                    'parent_sku' => 'required|string',
                    'sku' => 'required|string|unique:product_variations,sku|unique:products,sku',
                    'purchase_price' => 'required|numeric|min:0',
                    'sale_price' => 'required|numeric|min:0',
                    'tax' => 'nullable|exists:taxes,name',
                ]);

                if ($validator->fails()) {
                    $this->addError($rowIndex, $validator->errors()->first());
                    continue;
                }

                // Find the parent product we created and stored in the first pass
                $parentProduct = $parentProducts[$row['parent_sku']] ?? null;

                if (!$parentProduct) {
                    $this->addError($rowIndex, "Parent product with SKU '{$row['parent_sku']}' was not found or failed validation in the import file.");
                    continue;
                }

                // Create the variation linked to its parent
                $parentProduct->variations()->create([
                    'sku' => $row['sku'],
                    'measurement' => $row['measurement'] ?? null,
                    'cbm' => $row['cbm'] ?? null,
                    'weight' => $row['weight'] ?? null,
                    'purchase_price' => $row['purchase_price'],
                    'margin' => $row['margin'] ?? null,
                    'sale_price' => $row['sale_price'],
                    'tax_id' => $this->taxes[$row['tax']] ?? null,
                ]);
            }
        }
    }

    /**
     * Helper function to format and add an error message.
     * @param int $rowIndex
     * @param string $message
     */
    private function addError(int $rowIndex, string $message)
    {
        $this->errors[] = "Row " . ($rowIndex + 2) . ": " . $message;
    }
}
