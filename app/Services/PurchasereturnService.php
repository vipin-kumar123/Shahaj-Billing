<?php

namespace App\Services;

use App\Models\CompanyDetails;
use App\Models\PartyTransaction;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\StockMovement;
use App\Models\SupplierLedgers;
use Illuminate\Support\Facades\DB;

class PurchasereturnService
{
    public function purms($purchase_id)
    {
        return [
            'purchase' => Purchase::with(['supplier', 'items.product'])->findOrFail($purchase_id),
        ];
    }


    public function listData($userId)
    {
        return PurchaseReturn::with(['supplier', 'creator'])->select('*')->where('user_id', $userId);
    }


    public function storeReturn(array $data, $id)
    {
        DB::beginTransaction();

        try {

            $purchase = Purchase::with('items')->findOrFail($id);

            // CREATE RETURN HEADER
            $return = PurchaseReturn::create([
                'user_id'     => auth()->id(),
                'purchase_id' => $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'return_date' => $data['return_date'],
                'note'        => $data['note'] ?? null,
            ]);

            $totalReturnAmount = 0;

            foreach ($data['return_qty'] as $itemId => $qty) {

                if (!$qty || $qty <= 0) continue;

                $item = PurchaseItem::findOrFail($itemId);

                // Already returned qty check
                $alreadyReturned = PurchaseReturnItem::where('purchase_item_id', $itemId)
                    ->sum('return_qty');

                $availableQty = $item->quantity - $alreadyReturned;

                if ($qty > $availableQty) {
                    throw new \Exception("Return quantity cannot exceed available quantity.");
                }

                $gstAmt = (($item->unit_cost * $qty) * $item->gst_percent) / 100;
                $lineTotal = ($item->unit_cost * $qty) + $gstAmt;

                // SAVE RETURN ITEM
                PurchaseReturnItem::create([
                    'purchase_return_id' => $return->id,
                    'purchase_id'        => $purchase->id,
                    'purchase_item_id'   => $itemId,
                    'product_id'         => $item->product_id,
                    'unit_cost'          => $item->unit_cost,
                    'return_qty'         => $qty,
                    'gst_percent'        => $item->gst_percent,
                    'gst_amount'         => $gstAmt,
                    'total'              => $lineTotal
                ]);

                // STOCK DECREASE
                StockMovement::create([
                    'product_id'  => $item->product_id,
                    'user_id'     => auth()->id(),
                    'purchase_id' => $purchase->id,
                    'type'        => 'decrease',
                    'quantity'    => $qty,
                ]);

                $totalReturnAmount += $lineTotal;
            }

            // UPDATE RETURN TOTAL
            $return->update([
                'total_return_amount' => $totalReturnAmount
            ]);

            /*
        =====================================
         CORRECT DUE RECALCULATION
        =====================================
        */

            $totalReturned = PurchaseReturn::where('purchase_id', $purchase->id)
                ->sum('total_return_amount');

            $netPurchaseAmount = $purchase->total_amount - $totalReturned;

            $purchase->due_amount = $netPurchaseAmount - $purchase->paid_amount;

            $purchase->save();

            /*
        =====================================
        SUPPLIER LEDGER ENTRY
        =====================================
        */

            if ($totalReturnAmount > 0) {
                SupplierLedgers::create([
                    'user_id'     => auth()->id(),
                    'supplier_id' => $purchase->supplier_id,
                    'purchase_id' => $purchase->id,
                    'type'        => 'purchase_return',
                    'amount'      => $totalReturnAmount,
                    'note'        => "Purchase Return for Bill #" . $purchase->bill_no,
                ]);
            }

            DB::commit();

            return $return;
        } catch (\Exception $e) {

            DB::rollBack();
            throw $e;
        }
    }



    public function EditPurchaseReturn($return_id)
    {
        $pr = PurchaseReturn::with([
            'purchase.items',      // original purchase items
            'items.product'        // returned items
        ])->findOrFail($return_id);

        foreach ($pr->items as $rItem) {

            // Match return item with purchase item based on product_id
            $purchaseItem = $pr->purchase->items
                ->firstWhere('product_id', $rItem->product_id);

            // Add purchased qty for edit page
            $rItem->purchased_qty = $purchaseItem->quantity ?? 0;

            // MOST IMPORTANT (THIS FIXES YOUR ERROR)
            $rItem->purchase_item_id = $purchaseItem->id ?? null;
        }

        return $pr;
    }





    public function updateReturn(array $data, $returnId)
    {
        DB::beginTransaction();

        try {

            $return   = PurchaseReturn::with(['items', 'purchase'])->findOrFail($returnId);
            $purchase = $return->purchase;

            /*
        =====================================
        Reverse Old Effects Safely
        =====================================
        */

            // Delete stock movements created by this return
            foreach ($return->items as $rItem) {

                StockMovement::where('purchase_id', $purchase->id)
                    ->where('product_id', $rItem->product_id)
                    ->where('type', 'decrease')
                    ->orderByDesc('id')
                    ->limit(1)
                    ->delete();
            }

            // Delete only this return's ledger entry
            SupplierLedgers::where('purchase_id', $purchase->id)
                ->where('type', 'purchase_return')
                ->where('amount', $return->total_return_amount)
                ->orderByDesc('id')
                ->limit(1)
                ->delete();

            //Delete old return items
            $return->items()->delete();

            /*
        =====================================
        Apply New Return
        =====================================
        */

            $totalReturn = 0;

            foreach ($data['return_qty'] as $itemId => $qty) {

                if (!$qty || $qty <= 0) continue;

                $item = PurchaseItem::findOrFail($itemId);

                // Already returned excluding current return
                $alreadyReturned = PurchaseReturnItem::where('purchase_item_id', $itemId)
                    ->where('purchase_return_id', '!=', $return->id)
                    ->sum('return_qty');

                $availableQty = $item->quantity - $alreadyReturned;

                if ($qty > $availableQty) {
                    throw new \Exception("Return quantity exceeds available quantity.");
                }

                $gstAmt   = (($item->unit_cost * $qty) * $item->gst_percent) / 100;
                $lineTotal = ($item->unit_cost * $qty) + $gstAmt;

                PurchaseReturnItem::create([
                    'purchase_return_id' => $return->id,
                    'purchase_id'        => $purchase->id,
                    'purchase_item_id'   => $itemId,
                    'product_id'         => $item->product_id,
                    'unit_cost'          => $item->unit_cost,
                    'return_qty'         => $qty,
                    'gst_percent'        => $item->gst_percent,
                    'gst_amount'         => $gstAmt,
                    'total'              => $lineTotal
                ]);

                // Stock decrease (return to supplier)
                StockMovement::create([
                    'product_id'  => $item->product_id,
                    'user_id'     => auth()->id(),
                    'purchase_id' => $purchase->id,
                    'type'        => 'decrease',
                    'quantity'    => $qty,
                ]);

                $totalReturn += $lineTotal;
            }

            /*
        =====================================
        Update Return Header
        =====================================
        */

            $return->update([
                'return_date'         => $data['return_date'],
                'note'                => $data['note'] ?? null,
                'total_return_amount' => $totalReturn,
            ]);

            /*
        =====================================
        Recalculate Purchase Due (Correct Way)
        =====================================
        */

            $totalReturned = PurchaseReturn::where('purchase_id', $purchase->id)
                ->sum('total_return_amount');

            $netPurchaseAmount = $purchase->total_amount - $totalReturned;

            $purchase->due_amount = $netPurchaseAmount - $purchase->paid_amount;

            $purchase->save();

            /*
        =====================================
        Ledger Entry Again
        =====================================
        */

            if ($totalReturn > 0) {
                SupplierLedgers::create([
                    'user_id'     => auth()->id(),
                    'supplier_id' => $purchase->supplier_id,
                    'purchase_id' => $purchase->id,
                    'type'        => 'purchase_return',
                    'amount'      => $totalReturn,
                    'note'        => "Updated Purchase Return for Bill #" . $purchase->bill_no,
                ]);
            }

            DB::commit();

            return $return;
        } catch (\Exception $e) {

            DB::rollBack();
            throw $e;
        }
    }


    public function purchaseReturnShow(string $id)
    {
        $purchaseReturn = PurchaseReturn::with(['items.product', 'supplier', 'purchase'])
            ->findOrFail($id);

        $company = CompanyDetails::where('user_id', auth()->id())->first(); // if you have company table
        return compact('purchaseReturn', 'company');
    }


    public function DataGetPurchaseReturn(array $data)
    {
        // Load return + purchase + supplier + items
        return PurchaseReturn::with([
            'items',
            'purchase',
            'supplier'
        ])->find($data['returnId']);
    }


    public function saveReturnPayment(array $data, $id)
    {
        DB::beginTransaction();

        try {

            $purchaseReturn = PurchaseReturn::findOrFail($id);
            $purchase = Purchase::findOrFail($purchaseReturn->purchase_id);

            // STEP 1: Supplier Outstanding Calculation (Very important)
            $supplierOutstanding = PartyTransaction::where('party_id', $purchase->supplier_id)
                ->selectRaw("
                SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END) -
                SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) AS outstanding
            ")
                ->value('outstanding');

            // Supplier has no outstanding → refund logically impossible
            if ($supplierOutstanding <= 0) {
                DB::rollBack();
                throw new \Exception("Supplier has no outstanding balance. Refund cannot be processed.");
            }

            // Refund amount cannot be greater than outstanding
            if ($data['amount'] > $supplierOutstanding) {
                DB::rollBack();
                throw new \Exception("Refund amount cannot exceed supplier outstanding balance (₹$supplierOutstanding).");
            }

            // STEP 2: Purchase return refund pending calculation
            $totalRefunded = PartyTransaction::where('reference_type', 'purchase_return')
                ->where('reference_id', $purchaseReturn->id)
                ->sum('amount'); // credit = refund received

            $refundDue = $purchaseReturn->total_return_amount - $totalRefunded;

            if ($refundDue <= 0) {
                DB::rollBack();
                throw new \Exception("Refund for this purchase return is already settled.");
            }

            if ($data['amount'] > $refundDue) {
                DB::rollBack();
                throw new \Exception("Refund amount exceeds pending refund.");
            }

            // STEP 3: Store refund transaction (CREDIT because money coming to us)
            PartyTransaction::create([
                'user_id'        => auth()->id(),
                'party_id'       => $purchase->supplier_id,
                'reference_type' => 'purchase_return',
                'reference_id'   => $purchaseReturn->id,
                'type'           => 'credit',
                'amount'         => $data['amount'],
                'payment_method' => $data['payment_method'],
                'payment_date'   => $data['payment_date'],
                'note'           => "Refund for Purchase Return #" . $purchaseReturn->return_no,
            ]);

            // STEP 4: Update refunded amount & due amount
            $purchaseReturn->refunded_amount += $data['amount'];
            $purchaseReturn->refund_due = $purchaseReturn->total_return_amount - $purchaseReturn->refunded_amount;

            if ($purchaseReturn->refund_due == 0) {
                $purchaseReturn->refund_status = "REFUNDED";
            } elseif ($purchaseReturn->refunded_amount > 0) {
                $purchaseReturn->refund_status = "PARTIAL";
            }

            $purchaseReturn->note = $data['note'];
            $purchaseReturn->save();

            // STEP 5: Supplier ledger entry
            SupplierLedgers::create([
                'user_id'     => auth()->id(),
                'supplier_id' => $purchase->supplier_id,
                'purchase_id' => $purchase->id,
                'purchase_return_id' => $purchaseReturn->id,
                'type'        => 'refund',
                'amount'      => $data['amount'],
                'note'        => "Refund received for Purchase Return #" . $purchaseReturn->return_no,
            ]);

            DB::commit();
            return $purchaseReturn;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function returnPaymentHistory(array $data)
    {
        $purchaseReturn = PurchaseReturn::with(['supplier'])
            ->findOrFail($data['purchase_return_id']);

        // All refund transactions (CREDIT transactions)
        $transactions = PartyTransaction::where('reference_type', 'purchase_return')
            ->where('reference_id', $purchaseReturn->id)
            ->orderBy('payment_date', 'asc')
            ->get();

        return [
            'id'                 => $purchaseReturn->id,
            'return_no'          => $purchaseReturn->return_no,
            'return_date'        => formatDate($purchaseReturn->return_date),
            'total_return_amount' => $purchaseReturn->total_return_amount,

            // Refund tracking fields
            'refunded_amount'    => $purchaseReturn->refunded_amount,
            'refund_due'         => $purchaseReturn->refund_due,
            'refund_status'      => $purchaseReturn->refund_status,

            // Supplier information
            'supplier'           => $purchaseReturn->supplier,

            // Payment history list
            'transactions'    => $transactions->map(function ($t) {
                return [
                    'id'           => $t->id,
                    'amount'       => $t->amount,
                    'type'         => $t->type,
                    'payment_date' => formatDate($t->payment_date), // IMPORTANT
                ];
            }),
        ];
    }


    public function deleteReturnPayment(array $data)
    {
        DB::beginTransaction();

        try {
            $transaction = PartyTransaction::findOrFail($data['payment_id']);

            // Ensure this transaction belongs to purchase_return
            if ($transaction->reference_type !== "purchase_return") {
                throw new \Exception("Invalid refund transaction type.");
            }

            $purchaseReturn = PurchaseReturn::findOrFail($transaction->reference_id);

            // DELETE the refund transaction
            $transaction->delete();

            // Recalculate total refunded amount
            $totalRefunded = PartyTransaction::where('reference_type', 'purchase_return')
                ->where('reference_id', $purchaseReturn->id)
                ->sum('amount');

            // Update Purchase Return refund details
            $purchaseReturn->refunded_amount = $totalRefunded;
            $purchaseReturn->refund_due = $purchaseReturn->total_return_amount - $totalRefunded;

            if ($purchaseReturn->refund_due == 0) {
                $purchaseReturn->refund_status = "REFUNDED";
            } elseif ($totalRefunded > 0) {
                $purchaseReturn->refund_status = "PARTIAL";
            } else {
                $purchaseReturn->refund_status = "NOT PAID";
            }

            $purchaseReturn->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function returnPrintInvoive($id)
    {
        $purchaseReturn = PurchaseReturn::with(['items.product', 'supplier', 'purchase'])
            ->findOrFail($id);

        $company = CompanyDetails::where('user_id', auth()->id())->first(); // if you have company table
        return compact('purchaseReturn', 'company');
    }


    public function purchaseReturnInvoicePDF($id)
    {
        $purchaseReturn = PurchaseReturn::with(['items.product', 'supplier', 'purchase'])
            ->findOrFail($id);
        $company = CompanyDetails::where('user_id', auth()->id())->first();

        $cleanRef = preg_replace('/[\/\\\\]/', '-', $purchaseReturn->return_no);
        $fileName = 'Purchase_return' . $cleanRef . '.pdf';

        $logo = null;
        if ($company->company_logo && file_exists(public_path($company->company_logo))) {
            $logo = base64_encode(file_get_contents(public_path($company->company_logo)));
        }

        return [
            'purchaseReturn' => $purchaseReturn,
            'company'    => $company,
            'fileName'   => $fileName,
            'logo'       => $logo
        ];
    }
}
