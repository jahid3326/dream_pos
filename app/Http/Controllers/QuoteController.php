<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Quote;
use App\Models\PackGroupOption;
use App\Models\PackProduct;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDF;

class QuoteController extends Controller
{

    public function __construct()
    {
        $this->middleware('action.permission:Quote,read')->only('index');
        $this->middleware('action.permission:Quote,create')->only(['create', 'store']);
        $this->middleware('action.permission:Quote,show')->only('show');
        $this->middleware('action.permission:Quote,update')->only(['edit', 'update']);
        $this->middleware('action.permission:Quote,delete')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager-load all necessary nested data for the complex view
        $quotes = Quote::with([
            'customer.user',
            'payments',
            'categoryItems.product.category', // For standard items
            'categoryItems.variation', // For standard items
            // --- THIS IS THE CORRECTED EAGER LOADING FOR PACKS ---
            'packItems.constituentItems' => function ($query) {
                // For each constituent item, load its definition...
                $query->with([
                    'packProduct.product.category',
                    'packProductSelectedVariation' => function ($q) {
                        // ...and on that definition, load the final product and variation details.
                        $q->with(['product.category', 'variation']);
                    }
                ]);
            }
        ])->latest()->paginate(10);

        $quotesArray = $quotes->toArray();

        // Now $quotesArray is a pure PHP array
        /*
        echo '<pre>';
        print_r($quotesArray);
        echo '</pre>';
        */

        // Add calculated properties to each quote model for easier use in the view
        $quotes->each(function ($quote) {
            $quote->paid_amount = $quote->payments->sum('amount');
            $quote->due_amount = $quote->grand_total - $quote->paid_amount;
            $quote->payment_status = $quote->due_amount <= 0 ? 'Paid' : ($quote->paid_amount > 0 ? 'Deposit' : 'Unpaid');
        });
        return view('quotes.index', compact('quotes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'grand_total' => 'required|numeric',
            'items' => 'required|array|min:1',
        ]);

        $quote = null;

        try {
            DB::transaction(function () use ($request, &$quote) {
                $quote = $this->createQuoteRecord($request, 'on process');
                $this->createQuoteItems($quote, $request->items);
            });
        } catch (\Exception $e) {
            \Log::error('Quote Creation Failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error creating quote.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Quote generated successfully!',
            'redirect_url' => route('quotes.show', $quote->id)
        ]);

        /*
        $item_array = array();
        foreach ($request->items as $cartItem) {
            if ($cartItem['type'] === 'pack') {
                $option = PackGroupOption::find($cartItem['id']);
                if ($option) {
                    $packProducts = PackProduct::where('pack_group_option_id', $option->id)
                        ->with('product', 'selectedVariations.product', 'selectedVariations.variation.product')
                        ->get();

                    foreach ($packProducts as $packProduct) {
                        $selectedItems = $packProduct->selectedVariations;
                        if ($selectedItems->isNotEmpty()) {

                            foreach ($selectedItems as $selectedItem) {
                                $packSaleItem->constituentItems()->create([
                                    'pack_product_id' => $selectedItem->pack_product_id,
                                    'pack_product_selected_variation_id' => $selectedItem->id,
                                    'product_name' => $selectedItem->product->name ?? $selectedItem->product->name,
                                ]);
                            }

                            array_push($item_array, $selectedItems);
                        } else {
                            $packSaleItem->constituentItems()->create([
                                'pack_product_id' => $packProduct->id,
                                'product_name' => $packProduct->product->name ?? $packProduct->product->name,
                            ]);
                            array_push($item_array, $packProduct);
                        }
                    }
                }
            }
        }

        return response()->json([
            'items' => $item_array,
        ]);
        */
    }

    /**
     * Helper method to create the main Quote record.
     */
    private function createQuoteRecord(Request $request, string $status)
    {
        // 1. Find the latest quote record. We must order by ID to get the most recently created one.
        $latestQuote = Quote::latest('id')->first();

        $nextQuoteNumber = 1; // Default to 1 if no quotes exist yet

        if ($latestQuote) {
            // 2. Get the last quote number string (e.g., "QUOTE-01").
            $lastQuoteNumber = $latestQuote->quote_number;

            // 3. Extract the numeric part from the string.
            // This will find the number after the last hyphen "-".
            $numericPart = (int) substr($lastQuoteNumber, strrpos($lastQuoteNumber, '-') + 1);

            // 4. Increment the number.
            $nextQuoteNumber = $numericPart + 1;
        }

        // 5. Format the new quote number with consistent zero-padding.
        // Using a padding of 2 will give you QUOTE-01, QUOTE-02... QUOTE-10.
        // A padding of 4 (e.g., QUOTE-0001) is common for larger systems.
        $quoteNumber = 'QUOTE-' . str_pad($nextQuoteNumber, 2, '0', STR_PAD_LEFT);
        return Quote::create([
            'quote_number' => $quoteNumber,
            'customer_id' => $request->customer_id,
            'quote_date' => now(),
            'status' => $status,
            'sub_total' => $request->input('sub_total', 0),
            'order_tax_id' => $request->order_tax_id,
            'order_tax_amount' => $request->input('order_tax_amount', 0),
            'discount' => $request->input('discount_amount', 0),
            'discount_type' => $request->input('discount_type'),
            'discount_rate' => ($request->input('discount_type') === 'percentage') ? $request->input('discount_value') : null,
            'shipping' => $request->input('shipping', 0),
            'grand_total' => $request->grand_total,
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * Helper method to create all quote items (category and pack).
     */
    private function createQuoteItems(Quote $quote, array $items)
    {
        foreach ($items as $cartItem) {
            if ($cartItem['type'] === 'category') {
                $quote->categoryItems()->create([
                    'product_id' => $cartItem['id'],
                    'product_variation_id' => $cartItem['variation_id'],
                    'product_name' => $cartItem['name'],
                    'quantity' => $cartItem['quantity'],
                    'unit_price' => $cartItem['price'],
                    'total_price' => $cartItem['price'] * $cartItem['quantity'],
                ]);
            } elseif ($cartItem['type'] === 'pack') {
                // It's a pack, save the main line item to 'quote_pack_products'
                $packQuoteItem = $quote->packItems()->create([
                    'pack_group_option_id' => $cartItem['id'], // 'id' from the cart is the option_id
                    'pack_display_name' => $cartItem['name'],
                    'quantity' => $cartItem['quantity'],
                    'unit_price' => $cartItem['price'],
                    'total_price' => $cartItem['price'] * $cartItem['quantity'],
                ]);

                // --- THIS IS THE CORRECTED LOGIC FOR CONSTITUENT ITEMS ---

                // 1. Find the PackGroupOption that was sold.
                $option = PackGroupOption::find($cartItem['id']);

                if ($option) {
                    // 2. Query the 'pack_product' pivot table directly to get the link records.
                    //    This ensures we get the pivot model instances correctly.
                    $packProducts = PackProduct::where('pack_group_option_id', $option->id)
                        ->with('selectedVariations.product', 'selectedVariations.variation.product')
                        ->get();

                    // 3. Loop through the pivot records.
                    foreach ($packProducts as $packProduct) {
                        $selectedItems = $packProduct->selectedVariations;
                        if ($selectedItems->isNotEmpty()) {
                            foreach ($selectedItems as $selectedItem) {
                                $packQuoteItem->constituentItems()->create([
                                    'pack_product_id' => $selectedItem->pack_product_id,
                                    'pack_product_selected_variation_id' => $selectedItem->id,
                                    'product_name' => $selectedItem->product->name ?? $selectedItem->product->name,
                                ]);
                            }
                        } else {
                            $packQuoteItem->constituentItems()->create([
                                'pack_product_id' => $packProduct->id,
                                'product_name' => $packProduct->product->name ?? $packProduct->product->name,
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Quote $quote)
    {
        // Eager load all necessary data for the invoice
        $quote->load([
            'customer.user',
            'user', // The user who made the quote
            'orderTax',
            'categoryItems.product.category',
            'categoryItems.variation.product',
            'packItems.constituentItems.packProduct.product',
            'packItems.constituentItems.packProductSelectedVariation.variation.product',
            'payments'
        ]);

        // --- THIS IS THE KEY ADDITION ---
        // Calculate and add payment details directly to the quote object
        $quote->paid_amount = $quote->payments->sum('amount');
        $quote->due_amount = $quote->grand_total - $quote->paid_amount;
        $quote->payment_status = $quote->due_amount <= 0 ? 'Paid' : ($quote->paid_amount > 0 ? 'Deposit' : 'Unpaid');
        return view('quotes.show', compact('quote'));
    }

    public function viewQuotePdf(Quote $quote)
    {
        // Eager load all necessary data for the invoice
        $quote->load([
            'customer.user',
            'user', // The user who made the quote
            'orderTax',
            'categoryItems.product.category',
            'categoryItems.variation.product',
            'packItems.constituentItems.packProduct.product',
            'packItems.constituentItems.packProductSelectedVariation.variation.product',
            'payments'
        ]);

        // Calculate payment details
        $quote->paid_amount = $quote->payments->sum('amount');
        $quote->due_amount = $quote->grand_total - $quote->paid_amount;
        $quote->payment_status = $quote->due_amount <= 0 ? 'Paid' : ($quote->paid_amount > 0 ? 'Diposit' : 'Unpaid');

        // Pass the quote data to our dedicated PDF view
        $pdf = PDF::loadView('quotes.quote-pdf', compact('quote'));

        // --- THIS IS THE KEY CHANGE ---
        // Instead of download(), use stream().
        // This sets the Content-Disposition header to 'inline' instead of 'attachment'.
        $filename = 'Quote-' . $quote->quote_number . '.pdf';
        return $pdf->stream($filename);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quote $quote)
    {
        // Eager load all relationships needed to populate the form
        $quote->load('customer.user', 'categoryItems.product', 'categoryItems.variation', 'packItems', 'orderTax');

        $customers = Customer::with('user')->get();
        $taxes = Tax::where('status', true)->get();
        // echo '<pre>';
        // print_r($sale->toArray());
        // echo '</pre>';
        return view('quotes.edit', compact('quote', 'customers', 'taxes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quote $quote)
    {
        $request->validate([
            'quote_number' => 'required|string|unique:quotes,quote_number,' . $quote->id,
            'customer_id' => 'required|exists:customers,id',
            'quote_date' => 'required|date',
            'items' => 'sometimes|array',
        ]);

        try {
            DB::transaction(function () use ($request, $quote) {
                // 1. Update the main Quote record with the summary data
                $quote->update([
                    'quote_number' => $request->quote_number,
                    'customer_id' => $request->customer_id,
                    'quote_date' => $request->quote_date,
                    'status' => $request->status,
                    'sub_total' => $request->input('sub_total', 0),
                    'order_tax_id' => $request->order_tax_id,
                    'order_tax_amount' => $request->input('order_tax_amount', 0),
                    'discount' => $request->input('discount_amount', 0),
                    'discount_type' => $request->input('discount_type'),
                    'discount_rate' => $request->input('discount_value'),
                    'shipping' => $request->input('shipping', 0),
                    'grand_total' => $request->grand_total,
                ]);

                // --- THIS IS THE NEW DIFF AND SYNC LOGIC ---

                $submittedItems = collect($request->input('items', []));

                // Separate submitted items by type
                $submittedCategoryItems = $submittedItems->where('type', 'category');
                $submittedPackItems = $submittedItems->where('type', 'pack');

                // Get IDs of submitted items that already have a database ID
                $submittedCategoryItemIds = $submittedCategoryItems->pluck('quote_item_id')->filter();
                $submittedPackItemIds = $submittedPackItems->pluck('quote_item_id')->filter();

                // 1. DELETE items that are in the DB but not in the submission
                $quote->categoryItems()->whereNotIn('id', $submittedCategoryItemIds)->delete();
                $quote->packItems()->whereNotIn('id', $submittedPackItemIds)->delete();

                // 2. UPDATE existing items and CREATE new ones
                foreach ($submittedCategoryItems as $itemData) {
                    $quote->categoryItems()->updateOrCreate(
                        ['id' => $itemData['quote_item_id'] ?? null], // Condition to find the item
                        [ // Data to update or create with
                            'product_id' => $itemData['id'],
                            'product_variation_id' => $itemData['variation_id'] ?? null,
                            'product_name' => $itemData['name'],
                            'quantity' => $itemData['quantity'],
                            'unit_price' => $itemData['price'],
                            'total_price' => ($itemData['price'] * $itemData['quantity']),
                        ]
                    );
                }

                foreach ($submittedPackItems as $itemData) {
                    $packItem = $quote->packItems()->updateOrCreate(
                        ['id' => $itemData['quote_item_id'] ?? null],
                        [
                            'pack_group_option_id' => $itemData['id'],
                            'pack_display_name' => $itemData['name'],
                            'quantity' => $itemData['quantity'],
                            'unit_price' => $itemData['price'],
                            'total_price' => ($itemData['price'] * $itemData['quantity']),
                        ]
                    );

                    // After updating/creating the pack item, we must sync its constituent parts
                    // The simplest reliable way is still to delete and re-create for the sub-items
                    $packItem->constituentItems()->delete();
                    $option = PackGroupOption::find($itemData['id']);
                    if ($option) {
                        // ... (The logic from your createSaleItems to loop and create constituent items goes here)
                        // 2. Query the 'pack_product' pivot table directly to get the link records.
                        //    This ensures we get the pivot model instances correctly.
                        $packProducts = PackProduct::where('pack_group_option_id', $option->id)
                            ->with('selectedVariations.product', 'selectedVariations.variation.product')
                            ->get();

                        // 3. Loop through the pivot records.
                        foreach ($packProducts as $packProduct) {
                            $selectedItems = $packProduct->selectedVariations;
                            if ($selectedItems->isNotEmpty()) {
                                foreach ($selectedItems as $selectedItem) {
                                    $packItem->constituentItems()->create([
                                        'pack_product_id' => $selectedItem->pack_product_id,
                                        'pack_product_selected_variation_id' => $selectedItem->id,
                                        'product_name' => $selectedItem->product->name ?? $selectedItem->product->name,
                                    ]);
                                }
                            } else {
                                $packItem->constituentItems()->create([
                                    'pack_product_id' => $packProduct->id,
                                    'product_name' => $packProduct->product->name ?? $packProduct->product->name,
                                ]);
                            }
                        }
                    }
                }
            });
        } catch (\Exception $e) {
            \Log::error('Quote Update Failed: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()->with('error', 'An error occurred while updating the quote.')->withInput();
        }

        return redirect()->route('quotes.index')->with('success', 'Quote updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quote $quote)
    {
        // The onDelete('cascade') in the migration will delete all related quote_items
        $quote->delete();
        return redirect()->route('quotes.index')->with('success', 'Quote deleted successfully.');
    }
}
