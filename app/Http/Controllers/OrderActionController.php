<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\SupplierConfirmedOrder;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;

class OrderActionController extends Controller
{
    /**
     * Show the form for the supplier to propose modifications to an order.
     */
    public function showModificationForm($orderId)
    {
        $user = Auth::user();
        $supplier = $user->supplierProfile;

        $purchase = Purchase::find($orderId);

        // Get only the items for this specific supplier
        $items = $purchase->items()
            ->where('supplier_id', $supplier->id)
            ->with(['product.category', 'variation'])
            ->get();

        // Pass the data to the new modification view
        return view('supplier.orders.propose-modification', compact('purchase', 'items'));
    }

    public function proposeModification(Request $request, Purchase $purchase)
    {

        $supplier = $this->authorizeSupplier($purchase);

        $request->validate([
            'items' => 'present|array',
            'items.*.id' => 'required|exists:purchase_items,id',
            'items.*.quantity' => 'required|integer|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request, $purchase, $supplier) {
                $supplierSubTotal = 0;

                foreach ($request->input('items', []) as $itemData) {
                    // Find the original item to update
                    $item = PurchaseItem::where('id', $itemData['id'])
                        ->where('purchase_id', $purchase->id) // Security check
                        ->where('supplier_id', $supplier->id) // Security check
                        ->firstOrFail();

                    // Update the item with the proposed values
                    $item->quantity = $itemData['quantity'];
                    $item->unit_price = $itemData['unit_price'];
                    $item->total_price = $itemData['quantity'] * $itemData['unit_price'];
                    $item->save();

                    $supplierSubTotal += $item->total_price;
                }

                // After updating items, we need to recalculate the purchase's grand total.
                $newGrandTotal = $purchase->items()->sum('total_price');
                $purchase->total_amount = $newGrandTotal;
                $purchase->save();

                // Update the pivot table status for this specific supplier
                $purchase->suppliers()->updateExistingPivot($supplier->id, [
                    'status_review' => 'modification requested',
                    'status_production' => 'waiting', // Reset production status
                ]);
            });

            // Here, you would fire a notification to the admin to review the proposal.
            // e.g., $admin->notify(new SupplierProposedModification($purchase, $supplier));

        } catch (\Exception $e) {
            \Log::error('Modification proposal failed: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while submitting the proposal.');
        }

        return redirect()->route('orders.details', $purchase)->with('info', 'Your modification proposal has been sent to the admin for review.');
    }

    /**
     * A helper method to verify that the logged-in user is an authorized
     * supplier for this specific purchase order.
     *
     * @param \App\Models\Purchase $purchase
     * @return \App\Models\Supplier
     */
    private function authorizeSupplier(Purchase $purchase)
    {
        // Get the supplier profile of the currently authenticated user
        $supplier = Auth::user()->supplierProfile;

        // Security Check: If the supplier profile doesn't exist OR
        // if this purchase's list of suppliers does not contain this supplier's ID,
        // then the action is unauthorized.
        if (!$supplier || !$purchase->suppliers->contains($supplier->id)) {
            abort(403, 'You do not have permission to perform this action on this order.');
        }

        // If the check passes, return the supplier model for convenience
        return $supplier;
    }

    public function confirm(Request $request, Purchase $purchase)
    {
        // 1. Authorize the supplier and get their model
        $supplier = $this->authorizeSupplier($purchase);

        try {
            // 2. Update the pivot table status for this specific supplier
            $purchase->suppliers()->updateExistingPivot($supplier->id, [
                'status_review' => 'complet',
                'status_production' => 'in process',
            ]);

            // 3. Find and notify all users with the 'Super Admin' role.
            // We use whereHas to query based on the 'role' relationship.
            $superAdmins = User::whereHas('role', function ($query) {
                $query->where('name', 'Super Admin');
            })->get();

            if ($superAdmins->isNotEmpty()) {
                foreach ($superAdmins as $admin) {
                    $admin->notify(new SupplierConfirmedOrder($purchase, $supplier));
                }
            } else {
                // Log a warning if no Super Admin is found
                \Log::warning("Could not send SupplierConfirmedOrder notification: No Super Admin user found.");
            }
        } catch (\Exception $e) {
            \Log::error('Failed to confirm order: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while confirming the order.');
        }

        // Redirect back to the details page with a success message
        return redirect()->route('orders.details', $purchase)->with('success', 'Order has been confirmed and moved to production. The admin has been notified.');
    }
}
