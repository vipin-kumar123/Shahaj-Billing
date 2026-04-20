<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\CompanyDetails;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\PartyTransaction;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\Sales;
use App\Models\StockMovement;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function parms($userId)
    {
        $products = Product::where('is_active', 1)
            ->where('user_id', $userId)
            ->get();

        $customers = Customer::where('type', 'customer')
            ->where('user_id', $userId)
            ->where('is_active', 1)
            ->get();

        $brands = Brand::where('user_id', $userId)
            ->where('is_active', 1)
            ->get();

        return compact('products', 'customers', 'brands');
    }


    public function dataTable()
    {
        return Sales::with(['customer', 'creator'])->where('user_id', auth()->id())->latest();
    }


    public function saleSaved($data)
    {
        DB::beginTransaction();

        try {

            /*
        |----------------------------------------------------------
        | STOCK CHECK
        |----------------------------------------------------------
        */
            foreach ($data['product_id'] as $key => $productId) {

                $increase = StockMovement::where('product_id', $productId)
                    ->where('type', 'increase')
                    ->sum('quantity');

                $decrease = StockMovement::where('product_id', $productId)
                    ->where('type', 'decrease')
                    ->sum('quantity');

                if (($increase - $decrease) < $data['quantity'][$key]) {
                    throw new \Exception("Insufficient stock for selected product.");
                }
            }


            /*
        |----------------------------------------------------------
        | CALCULATE TOTAL DISCOUNT
        |----------------------------------------------------------
        */
            $totalDiscount = 0;

            foreach ($data['product_id'] as $key => $productId) {

                $qty   = (float) $data['quantity'][$key];
                $price = (float) $data['unit_price'][$key];
                $discountValue = (float) ($data['discount'][$key] ?? 0);
                $discountType  = $data['discount_type'][$key] ?? 'flat';

                $lineBase = $qty * $price;

                if ($discountType === 'percent') {
                    $lineDiscount = ($lineBase * $discountValue) / 100;
                } else {
                    $lineDiscount = $discountValue;
                }

                $totalDiscount += $lineDiscount;
            }


            /*
        |----------------------------------------------------------
        | CREATE SALE
        |----------------------------------------------------------
        */
            $sale = Sales::create([
                'user_id'        => $data['user_id'],
                'customer_id'    => $data['customer_id'],
                'sale_date'      => $data['sale_date'],
                'invoice_no'     => $data['invoice_no'],
                'reference_no'   => $data['reference_no'],
                'payment_status' => 'DUE',
                'subtotal'       => $data['subtotal'],
                'discount_amount' => round($totalDiscount, 2), // ✅ save summary
                'tax_amount'     => $data['tax_amount'],
                'shipping_charges' => $data['shipping_charges'],
                'rounding'       => $data['rounding'],
                'total_amount'   => $data['total_amount'],
                'paid_amount'    => 0,
                'due_amount'     => $data['total_amount'],
                'payment_method' => $data['payment_method'] ?? null,
                'notes'          => $data['notes'],
                'created_by'     => $data['user_id'],
            ]);


            /*
        |----------------------------------------------------------
        | SAVE ITEMS + STOCK DECREASE
        |----------------------------------------------------------
        */
            foreach ($data['product_id'] as $key => $productId) {

                SaleItem::create([
                    'sale_id'       => $sale->id,
                    'product_id'    => $productId,
                    'quantity'      => $data['quantity'][$key],
                    'price'         => $data['unit_price'][$key],
                    'discount'      => $data['discount'][$key] ?? 0,
                    'discount_type' => $data['discount_type'][$key] ?? 'flat',
                    'tax_percent'   => $data['gst_percent'][$key],
                    'tax_amount'    => $data['gst_amount'][$key],
                    'subtotal'      => $data['total'][$key],
                ]);

                StockMovement::create([
                    'product_id' => $productId,
                    'user_id'    => $data['user_id'],
                    'sale_id'    => $sale->id,
                    'type'       => 'decrease',
                    'quantity'   => $data['quantity'][$key],
                ]);
            }


            /*
        |----------------------------------------------------------
        | CUSTOMER LEDGER (SALE ENTRY)
        |----------------------------------------------------------
        */
            CustomerLedger::create([
                'user_id'     => $data['user_id'],
                'customer_id' => $sale->customer_id,
                'sale_id'     => $sale->id,
                'type'        => 'debit',
                'amount'      => $sale->total_amount,
                'note'        => 'Sale Invoice #' . $sale->invoice_no,
            ]);


            /*
        |----------------------------------------------------------
        | INITIAL PAYMENT (IF ANY)
        |----------------------------------------------------------
        */
            if (!empty($data['paid_amount']) && $data['paid_amount'] > 0) {

                PartyTransaction::create([
                    'user_id'        => $data['user_id'],
                    'party_id'       => $sale->customer_id,
                    'reference_type' => 'sale',
                    'reference_id'   => $sale->id,
                    'type'           => 'credit',
                    'amount'         => $data['paid_amount'],
                    'payment_method' => $data['payment_method'] ?? 'cash',
                    'payment_date'   => $data['sale_date'],
                    'note'           => 'Initial payment for Sale #' . $sale->invoice_no,
                ]);

                CustomerLedger::create([
                    'user_id'     => $data['user_id'],
                    'customer_id' => $sale->customer_id,
                    'sale_id'     => $sale->id,
                    'type'        => 'credit',
                    'amount'      => $data['paid_amount'],
                    'note'        => 'Payment received for Sale #' . $sale->invoice_no,
                ]);
            }


            /*
        |----------------------------------------------------------
        | RECALCULATE PAID & DUE
        |----------------------------------------------------------
        */
            $totalPaid = PartyTransaction::where('reference_type', 'sale')
                ->where('reference_id', $sale->id)
                ->sum('amount');

            $sale->paid_amount = $totalPaid;
            $sale->due_amount  = $sale->total_amount - $totalPaid;

            if ($sale->due_amount <= 0) {
                $sale->payment_status = 'PAID';
                $sale->due_amount = 0;
            } elseif ($totalPaid > 0) {
                $sale->payment_status = 'PARTIAL';
            } else {
                $sale->payment_status = 'DUE';
            }

            $sale->save();

            DB::commit();
            return $sale;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }



    public function saleDetails($id)
    {
        $sale = Sales::with(['items.product', 'customer'])->where('user_id', auth()->id())->findOrFail($id);
        $company = CompanyDetails::where('user_id', auth()->id())->first();
        return compact('sale', 'company');
    }

    public function saleEdit($id)
    {
        $sale = Sales::with(['items.product', 'customer'])->where('user_id', auth()->id())->findOrFail($id);
        return compact('sale');
    }

    public function updateSale($data, $id)
    {
        DB::beginTransaction();

        try {

            $sale = Sales::findOrFail($id);

            /*
        |----------------------------------------------------------
        | REVERSE OLD STOCK
        |----------------------------------------------------------
        */
            $oldItems = SaleItem::where('sale_id', $sale->id)->get();

            foreach ($oldItems as $oldItem) {
                StockMovement::create([
                    'product_id' => $oldItem->product_id,
                    'user_id'    => auth()->id(),
                    'sale_id'    => $sale->id,
                    'type'       => 'increase',
                    'quantity'   => $oldItem->quantity,
                ]);
            }

            // Delete old sale items
            SaleItem::where('sale_id', $sale->id)->delete();


            /*
        |----------------------------------------------------------
        | STOCK CHECK
        |----------------------------------------------------------
        */
            foreach ($data['product_id'] as $key => $productId) {

                $increase = StockMovement::where('product_id', $productId)
                    ->where('type', 'increase')
                    ->sum('quantity');

                $decrease = StockMovement::where('product_id', $productId)
                    ->where('type', 'decrease')
                    ->sum('quantity');

                if (($increase - $decrease) < $data['quantity'][$key]) {
                    throw new \Exception("Insufficient stock for selected product.");
                }
            }


            /*
        |----------------------------------------------------------
        | CALCULATE TOTAL DISCOUNT
        |----------------------------------------------------------
        */
            $totalDiscount = 0;

            foreach ($data['product_id'] as $key => $productId) {

                $qty   = (float) $data['quantity'][$key];
                $price = (float) $data['unit_price'][$key];
                $discountValue = (float) ($data['discount'][$key] ?? 0);
                $discountType  = $data['discount_type'][$key] ?? 'flat';

                $lineBase = $qty * $price;

                if ($discountType === 'percent') {
                    $lineDiscount = ($lineBase * $discountValue) / 100;
                } else {
                    $lineDiscount = $discountValue;
                }

                $totalDiscount += $lineDiscount;
            }


            /*
        |----------------------------------------------------------
        | UPDATE SALE HEADER
        |----------------------------------------------------------
        */
            $sale->update([
                'customer_id'      => $data['customer_id'],
                'sale_date'        => $data['sale_date'],
                'invoice_no'       => $data['invoice_no'],
                'subtotal'         => $data['subtotal'],
                'discount_amount'  => round($totalDiscount, 2), // ✅ save total discount
                'tax_amount'       => $data['tax_amount'],
                'shipping_charges' => $data['shipping_charges'],
                'rounding'         => $data['rounding'],
                'total_amount'     => $data['total_amount'],
                'notes'            => $data['notes'],
            ]);


            /*
        |----------------------------------------------------------
        | INSERT NEW ITEMS + STOCK DECREASE
        |----------------------------------------------------------
        */
            foreach ($data['product_id'] as $key => $productId) {

                SaleItem::create([
                    'sale_id'       => $sale->id,
                    'product_id'    => $productId,
                    'quantity'      => $data['quantity'][$key],
                    'price'         => $data['unit_price'][$key],
                    'discount'      => $data['discount'][$key] ?? 0,
                    'discount_type' => $data['discount_type'][$key] ?? 'flat',
                    'tax_percent'   => $data['gst_percent'][$key],
                    'tax_amount'    => $data['gst_amount'][$key],
                    'subtotal'      => $data['total'][$key],
                ]);

                StockMovement::create([
                    'product_id' => $productId,
                    'user_id'    => $data['user_id'],
                    'sale_id'    => $sale->id,
                    'type'       => 'decrease',
                    'quantity'   => $data['quantity'][$key],
                ]);
            }


            /*
        |----------------------------------------------------------
        | UPDATE CUSTOMER LEDGER
        |----------------------------------------------------------
        */
            CustomerLedger::where('sale_id', $sale->id)
                ->where('type', 'debit')
                ->delete();

            CustomerLedger::create([
                'user_id'     => $data['user_id'],
                'customer_id' => $sale->customer_id,
                'sale_id'     => $sale->id,
                'type'        => 'debit',
                'amount'      => $sale->total_amount,
                'note'        => 'Updated Sale Invoice #' . $sale->invoice_no,
            ]);


            /*
        |----------------------------------------------------------
        | RECALCULATE PAYMENT
        |----------------------------------------------------------
        */
            $totalPaid = PartyTransaction::where('reference_type', 'sale')
                ->where('reference_id', $sale->id)
                ->sum('amount');

            $sale->paid_amount = $totalPaid;
            $sale->due_amount  = $sale->total_amount - $totalPaid;

            if ($sale->due_amount <= 0) {
                $sale->payment_status = 'PAID';
                $sale->due_amount = 0;
            } elseif ($totalPaid > 0) {
                $sale->payment_status = 'PARTIAL';
            } else {
                $sale->payment_status = 'DUE';
            }

            $sale->save();

            DB::commit();
            return $sale;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function deleteSale(array $data)
    {
        DB::beginTransaction();

        try {

            $sale = Sales::with('items')->findOrFail($data['sale_id']);

            //Block delete if return exists
            if (SaleReturn::where('sale_id', $sale->id)->exists()) {
                throw new \Exception("Cannot delete sale. Sale return exists.");
            }

            /*
        |--------------------------------------------------
        | REVERSE STOCK
        |--------------------------------------------------
        */
            foreach ($sale->items as $item) {

                StockMovement::create([
                    'product_id' => $item->product_id,
                    'user_id'    => auth()->id(),
                    'sale_id'    => $sale->id,
                    'type'       => 'increase', // reverse sale
                    'quantity'   => $item->quantity,
                ]);
            }

            /*
        |--------------------------------------------------
        | DELETE SALE ITEMS
        |--------------------------------------------------
        */
            SaleItem::where('sale_id', $sale->id)->delete();

            /*
        |--------------------------------------------------
        | DELETE CUSTOMER LEDGER ENTRIES
        |--------------------------------------------------
        */
            CustomerLedger::where('sale_id', $sale->id)->delete();

            /*
        |--------------------------------------------------
        | DELETE PARTY TRANSACTIONS
        |--------------------------------------------------
        */
            PartyTransaction::where('reference_type', 'sale')
                ->where('reference_id', $sale->id)
                ->delete();

            /*
        |--------------------------------------------------
        | DELETE SALE
        |--------------------------------------------------
        */
            $sale->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {

            DB::rollBack();
            throw $e;
        }
    }

    public function receive_payment_data(array $data)
    {
        return Sales::with(['customer'])->findOrFail($data['id']);
    }

    public function receiveAmount(array $data, $id)
    {
        DB::beginTransaction();

        try {

            $sale = Sales::findOrFail($id);

            /*
        |-------------------------------------------
        | Recalculate Correct Due Dynamically
        |-------------------------------------------
        */

            // Total returned
            $totalReturned = SaleReturn::where('sale_id', $sale->id)
                ->sum('total_return_amount');

            $netSale = $sale->total_amount - $totalReturned;

            $currentDue = $netSale - $sale->paid_amount;

            // If already fully settled or advance
            if ($currentDue <= 0) {
                DB::rollBack();
                throw new \Exception("This invoice is already fully settled or in advance.");
            }

            // Prevent overpayment
            if ($data['amount'] > $currentDue) {
                DB::rollBack();
                throw new \Exception("Payment amount exceeds current due.");
            }

            /*
        |-------------------------------------------
        | Save Party Transaction (Customer)
        |-------------------------------------------
        */

            $transaction = PartyTransaction::create([
                'user_id'        => auth()->id(),
                'party_id'       => $sale->customer_id,
                'party_type'     => 'customer',
                'reference_type' => 'sale',
                'reference_id'   => $sale->id,
                'type'           => 'credit', // 💡 important difference
                'amount'         => $data['amount'],
                'payment_method' => $data['payment_method'],
                'payment_date'   => Carbon::createFromFormat('d-m-Y', $data['payment_date'])->format('Y-m-d'),
                'note'           => 'Payment received for Invoice #' . $sale->invoice_no,
            ]);

            /*
        |-------------------------------------------
        | Update Paid Amount
        |-------------------------------------------
        */

            $sale->paid_amount += $data['amount'];

            // Recalculate due again
            $sale->due_amount = $netSale - $sale->paid_amount;

            /*
        |-------------------------------------------
        | Update Payment Status
        |-------------------------------------------
        */

            if ($sale->due_amount == 0) {
                $sale->payment_status = "PAID";
            } elseif ($sale->paid_amount > 0) {
                $sale->payment_status = "PARTIAL";
            }

            $sale->save();

            /*
        |-------------------------------------------
        | Customer Ledger Entry
        |-------------------------------------------
        */

            CustomerLedger::create([
                'user_id'               => auth()->id(),
                'customer_id'           => $sale->customer_id,
                'sale_id'               => $sale->id,
                'type'                  => 'payment',
                'amount'                => $data['amount'],
                'party_transaction_id'  => $transaction->id,
                'note'                  => "Payment against Invoice #" . $sale->invoice_no,
            ]);

            DB::commit();

            return true;
        } catch (\Exception $e) {

            DB::rollBack();
            throw $e;
        }
    }

    public function saleReceiveHistory(array $data)
    {
        $sale = Sales::with(['customer', 'payments'])
            ->find($data['id']);

        return [
            'success'   => true,
            'data'      => $sale,
            'payments'  => $sale->payments,
            'total_paid' => $sale->paid_amount,
            'due_amount' => $sale->due_amount,
            'invoice_no' => $sale->invoice_no,
            'sale_date'  => formatDate($sale->sale_date)
        ];
    }

    public function deletePayment(array $data)
    {
        DB::beginTransaction();

        try {
            // GET THE PAYMENT ENTRY
            $transaction = PartyTransaction::findOrFail($data['payment_id']);

            // Ensure this is a SALE payment entry
            if ($transaction->reference_type !== 'sale') {
                throw new \Exception("Invalid payment record. This is not a sale payment.");
            }

            $saleId = $transaction->reference_id;

            //  GET SALE DETAILS
            $sale = Sales::findOrFail($saleId);

            // DELETE CUSTOMER LEDGER ENTRY
            CustomerLedger::where('sale_id', $sale->id)
                ->where('type', 'payment')
                ->where('party_transaction_id', $transaction->id)
                ->delete();

            // DELETE PARTY TRANSACTION RECORD
            $transaction->delete();

            // RECALCULATE THE TOTAL PAID AMOUNT AFTER DELETE
            $totalPaid = PartyTransaction::where('reference_type', 'sale')
                ->where('reference_id', $sale->id)
                ->sum('amount');

            // UPDATE SALE FIELDS (paid / due / status)
            $sale->paid_amount = $totalPaid;
            $sale->due_amount = $sale->total_amount - $totalPaid;

            //  UPDATE PAYMENT STATUS
            if ($sale->due_amount == 0) {
                $sale->payment_status = 'PAID';
            } elseif ($sale->paid_amount > 0) {
                $sale->payment_status = 'PARTIAL';
            } else {
                $sale->payment_status = 'UNPAID';
            }

            $sale->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {

            DB::rollBack();
            throw $e;
        }
    }

    /**********************SALE DATA GET************************************/
    public function saleData($sale_id)
    {
        $sale = Sales::with(['items.product', 'customer'])->findOrFail($sale_id);
        return compact('sale');
    }


    /**********************SALE RETURN START************************************/
    public function storeSaleReturn(array $data, $saleId)
    {
        DB::beginTransaction();

        try {

            $sale = Sales::with('items')->findOrFail($saleId);

            /*
        |--------------------------------------------------------------------------
        | 1) CREATE RETURN HEADER
        |--------------------------------------------------------------------------
        */
            $return = SaleReturn::create([
                'user_id'            => auth()->id(),
                'sale_id'            => $sale->id,
                'customer_id'        => $sale->customer_id,
                'return_no'          => 'SR-' . time(),
                'return_date'        => $data['return_date'],
                'note'               => $data['note'] ?? null,
                'refunded_amount'    => 0,
                'refund_due'         => 0,
                'refund_status'      => 'NOT PAID',
            ]);

            $totalReturnAmount = 0;

            /*
        |--------------------------------------------------------------------------
        | 2) PROCESS EACH RETURN ITEM
        |--------------------------------------------------------------------------
        */
            foreach ($data['return_qty'] as $saleItemId => $qty) {

                if (!$qty || $qty <= 0) continue;

                $item = SaleItem::findOrFail($saleItemId);

                // Already returned qty
                $alreadyReturned = SaleReturnItem::where('sale_item_id', $saleItemId)
                    ->sum('return_qty');

                $availableQty = $item->quantity - $alreadyReturned;

                if ($qty > $availableQty) {
                    throw new \Exception("Return quantity exceeds sold quantity.");
                }

                // GST & line total
                $gstAmt = (($item->price * $qty) * $item->tax_percent) / 100;
                $lineTotal = ($item->price * $qty) + $gstAmt;

                // Create Return Item
                SaleReturnItem::create([
                    'sale_return_id' => $return->id,
                    'sale_id'        => $sale->id,
                    'sale_item_id'   => $saleItemId,
                    'product_id'     => $item->product_id,
                    'unit_price'     => $item->price,
                    'return_qty'     => $qty,
                    'gst_percent'    => $item->tax_percent,
                    'gst_amount'     => $gstAmt,
                    'total'          => $lineTotal,
                ]);

                // STOCK INCREASE (Customer ne maal return kiya)
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'user_id'    => auth()->id(),
                    'sale_id'    => $sale->id,
                    'type'       => 'increase',
                    'quantity'   => $qty,
                ]);

                $totalReturnAmount += $lineTotal;
            }

            /*
        |--------------------------------------------------------------------------
        | UPDATE RETURN TOTAL + REFUND DUE
        |--------------------------------------------------------------------------
        */
            $return->update([
                'total_return_amount' => $totalReturnAmount,
                'refund_due'          => $totalReturnAmount,
                'refunded_amount'     => 0,
                'refund_status'       => 'NOT PAID',
            ]);

            /*
        |--------------------------------------------------------------------------
        |UPDATE SALE DUE AMOUNT
        |--------------------------------------------------------------------------
        */

            $totalReturnAmountAll = SaleReturn::where('sale_id', $sale->id)
                ->sum('total_return_amount');

            $netSaleAmount = $sale->total_amount - $totalReturnAmountAll;

            $sale->due_amount = $netSaleAmount - $sale->paid_amount;
            $sale->save();

            /*
        |--------------------------------------------------------------------------
        |LEDGER ENTRY – CUSTOMER CREDIT
        |--------------------------------------------------------------------------
        */
            if ($totalReturnAmount > 0) {
                CustomerLedger::create([
                    'customer_id' => $sale->customer_id,
                    'sale_id'     => $sale->id,
                    'type'        => 'credit',
                    'amount'      => $totalReturnAmount,
                    'note'        => 'Sale Return #' . $return->return_no,
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        |PARTY TRANSACTION — CREDIT (VERY IMPORTANT)
        |--------------------------------------------------------------------------
        */
            PartyTransaction::create([
                'user_id'        => auth()->id(),
                'party_id'       => $sale->customer_id,
                'reference_type' => 'sale_return',
                'reference_id'   => $return->id,
                'type'           => 'credit',  // Return = customer ko paisa dena
                'amount'         => $totalReturnAmount,
                'payment_method' => 'Auto-Return',
                'payment_date'   => now(),
                'note'           => 'Auto entry for Sale Return #' . $return->return_no,
            ]);

            DB::commit();

            return $return;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }




    public function returnDataTable()
    {
        return SaleReturn::with('customer', 'creator')->where('user_id', auth()->id())->latest();
    }

    public function editSaleReturn($id)
    {
        $saleReturn = SaleReturn::with(['sale.items.product', 'items'])
            ->findOrFail($id);

        $sale = $saleReturn->sale;
        return compact('saleReturn', 'sale');
    }


    public function updateSaleReturn(array $data, $returnId)
    {
        DB::beginTransaction();

        try {

            $return = SaleReturn::with('items')->findOrFail($returnId);
            $sale   = Sales::findOrFail($return->sale_id);

            /*
        =====================================
         Reverse Old Effects
        =====================================
        */

            // Reverse Stock Movements created by this return
            foreach ($return->items as $oldItem) {

                StockMovement::where('sale_id', $sale->id)
                    ->where('product_id', $oldItem->product_id)
                    ->where('type', 'increase')
                    ->orderByDesc('id')
                    ->limit(1)
                    ->delete();
            }

            // Reverse Ledger entry created by this return
            CustomerLedger::where('sale_id', $sale->id)
                ->where('type', 'credit')
                ->where('amount', $return->total_return_amount)
                ->orderByDesc('id')
                ->limit(1)
                ->delete();

            // Delete old return items
            $return->items()->delete();

            /*
        =====================================================================================================================
         Apply New Return
        =====================================================================================================================
        */

            $totalReturnAmount = 0;

            foreach ($data['return_qty'] as $saleItemId => $qty) {

                if (!$qty || $qty <= 0) continue;

                $item = SaleItem::findOrFail($saleItemId);

                // Already returned from other returns
                $alreadyReturned = SaleReturnItem::where('sale_item_id', $saleItemId)
                    ->where('sale_return_id', '!=', $return->id)
                    ->sum('return_qty');

                $availableQty = $item->quantity - $alreadyReturned;

                if ($qty > $availableQty) {
                    throw new \Exception("Return quantity exceeds sold quantity.");
                }

                $gstAmt = (($item->price * $qty) * $item->tax_percent) / 100;
                $lineTotal = ($item->price * $qty) + $gstAmt;

                // Create return item
                SaleReturnItem::create([
                    'sale_return_id' => $return->id,
                    'sale_id'        => $sale->id,
                    'sale_item_id'   => $saleItemId,
                    'product_id'     => $item->product_id,
                    'unit_price'     => $item->price,
                    'return_qty'     => $qty,
                    'gst_percent'    => $item->tax_percent,
                    'gst_amount'     => $gstAmt,
                    'total'          => $lineTotal,
                ]);

                // Stock increase
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'user_id'    => auth()->id(),
                    'sale_id'    => $sale->id,
                    'type'       => 'increase',
                    'quantity'   => $qty,
                ]);

                $totalReturnAmount += $lineTotal;
            }

            /*
        =====================================
        Update Header
        =====================================
        */

            $return->update([
                'return_date'         => $data['return_date'],
                'note'                => $data['note'] ?? null,
                'total_return_amount' => $totalReturnAmount
            ]);

            /*
        =====================================
        Recalculate Sale Due Properly
        =====================================
        */

            // Total returned from all returns
            $totalReturned = SaleReturn::where('sale_id', $sale->id)
                ->sum('total_return_amount');

            $netSaleAmount = $sale->total_amount - $totalReturned;

            $sale->due_amount = $netSaleAmount - $sale->paid_amount;

            // if ($sale->due_amount < 0) {
            //     $sale->due_amount = 0;
            // }

            $sale->save();

            /*
        =====================================
        Ledger Credit Again
        =====================================
        */

            if ($totalReturnAmount > 0) {
                CustomerLedger::create([
                    'customer_id' => $sale->customer_id,
                    'sale_id'     => $sale->id,
                    'type'        => 'credit',
                    'amount'      => $totalReturnAmount,
                    'note'        => 'Sale Return #' . $return->return_no,
                ]);
            }

            DB::commit();

            return $return;
        } catch (\Exception $e) {

            DB::rollBack();
            throw $e;
        }
    }


    public function returnShow(string $saleReturId)
    {
        $saleReturn = SaleReturn::with(['items.product', 'customer', 'sale'])
            ->findOrFail($saleReturId);
        $company = CompanyDetails::where('user_id', auth()->id())->first();
        return compact('saleReturn', 'company');
    }


    public function refundUsedSaleReturnData(array $data)
    {
        return SaleReturn::with(['customer', 'sale'])
            ->findOrFail($data['sale_return_id']);
    }


    public function saveRefundPayment(array $data, $id)
    {
        DB::beginTransaction();

        try {
            $saleReturn = SaleReturn::findOrFail($id);
            $sale = Sales::findOrFail($saleReturn->sale_id);

            /*
        |--------------------------------------------------------------------------
        | CUSTOMER OUTSTANDING
        |--------------------------------------------------------------------------
        */
            $customerOutstanding = PartyTransaction::where('party_id', $sale->customer_id)
                ->selectRaw("
                SUM(CASE WHEN type='credit' THEN amount ELSE 0 END) -
                SUM(CASE WHEN type='debit' THEN amount ELSE 0 END) AS outstanding
            ")
                ->value('outstanding');

            if ($customerOutstanding <= 0) {
                throw new \Exception("Customer has no outstanding balance. Refund cannot be processed.");
            }

            if ($data['amount'] > $customerOutstanding) {
                throw new \Exception("Refund amount cannot exceed customer outstanding balance (₹{$customerOutstanding}).");
            }

            /*
        |--------------------------------------------------------------------------
        | TOTAL REFUND ALREADY GIVEN
        |--------------------------------------------------------------------------
        | ONLY debit entries count
        |--------------------------------------------------------------------------
        */
            $totalRefunded = PartyTransaction::where('reference_type', 'sale_return')
                ->where('reference_id', $saleReturn->id)
                ->where('type', 'debit')  //FIXED
                ->sum('amount');

            $refundDue = $saleReturn->total_return_amount - $totalRefunded;

            if ($refundDue <= 0) {
                throw new \Exception("Refund for this sale return is already settled.");
            }

            if ($data['amount'] > $refundDue) {
                throw new \Exception("Refund amount exceeds pending refund.");
            }

            /*
        |--------------------------------------------------------------------------
        |CREATE REFUND TRANSACTION (DEBIT)
        |--------------------------------------------------------------------------
        */
            PartyTransaction::create([
                'user_id'        => auth()->id(),
                'party_id'       => $sale->customer_id,
                'reference_type' => 'sale_return',
                'reference_id'   => $saleReturn->id,
                'type'           => 'debit', // Refund diya = debit
                'amount'         => $data['amount'],
                'payment_method' => $data['payment_method'],
                'payment_date'   => $data['payment_date'],
                'note'           => "Refund issued for Sale Return #" . $saleReturn->return_no,
            ]);

            /*
        |--------------------------------------------------------------------------
        |UPDATE SALE RETURN FIELDS
        |--------------------------------------------------------------------------
        */
            $saleReturn->refunded_amount = $totalRefunded + $data['amount'];
            $saleReturn->refund_due      = $saleReturn->total_return_amount - $saleReturn->refunded_amount;

            if ($saleReturn->refund_due == 0) {
                $saleReturn->refund_status = "REFUNDED";
            } elseif ($saleReturn->refunded_amount > 0) {
                $saleReturn->refund_status = "PARTIAL";
            }

            $saleReturn->save();

            /*
        |--------------------------------------------------------------------------
        |LEDGER ENTRY
        |--------------------------------------------------------------------------
        */
            CustomerLedger::create([
                'user_id'         => auth()->id(),
                'customer_id'     => $sale->customer_id,
                'sale_id'         => $sale->id,
                'sale_return_id'  => $saleReturn->id,
                'type'            => 'refund',
                'amount'          => $data['amount'],
                'note'            => "Refund for Sale Return #" . $saleReturn->return_no,
            ]);

            DB::commit();
            return $saleReturn;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function saleReturnHistory(array $data)
    {
        $saleReturn = SaleReturn::with(['customer', 'sale'])->findOrFail($data['sale_return_id']);

        // Get only refund entries (DEBIT)
        $payments = PartyTransaction::where('reference_type', 'sale_return')
            ->where('reference_id', $saleReturn->id)
            ->where('type', 'debit')
            ->orderBy('payment_date', 'asc')
            ->get();

        return [
            'id'                 => $saleReturn->id,
            'return_no'          => $saleReturn->return_no,
            'return_date'        => formatDate($saleReturn->return_date),
            'total_return_amount' => $saleReturn->total_return_amount,
            'refunded_amount'    => $saleReturn->refunded_amount,
            'refund_due'         => $saleReturn->refund_due,
            'refund_status'      => $saleReturn->refund_status,

            'customer' => [
                'name' => $saleReturn->customer->first_name . ' ' . $saleReturn->customer->last_name,
                'phone' => $saleReturn->customer->mobile_number,
                'address' => $saleReturn->customer->billing_address,
            ],

            'sale' => [
                'invoice_no' => $saleReturn->sale->invoice_no,
                'date'       => formatDate($saleReturn->sale->sale_date),
                'total'      => $saleReturn->sale->total_amount,
            ],

            'payments' => $payments->map(function ($t) {
                return [
                    'id'           => $t->id,
                    'amount'       => $t->amount,
                    'payment_method' => $t->payment_method,
                    'payment_date' => formatDate($t->payment_date),
                ];
            }),
        ];
    }


    public function deleteRefundPayment(array $data)
    {
        DB::beginTransaction();

        try {

            $transaction = PartyTransaction::findOrFail($data['payment_id']);

            // Must be sale_return + debit (refund)
            if ($transaction->reference_type !== 'sale_return' || $transaction->type !== 'debit') {
                throw new \Exception("Invalid refund transaction type.");
            }

            $saleReturn = SaleReturn::findOrFail($transaction->reference_id);

            // 1. Delete the refund transaction
            $transaction->delete();

            // 2. Recalculate refunded amount (only DEBIT type)
            $totalRefunded = PartyTransaction::where('reference_type', 'sale_return')
                ->where('reference_id', $saleReturn->id)
                ->where('type', 'debit')   // refund only
                ->sum('amount');

            // 3. Update refund fields
            $saleReturn->refunded_amount = $totalRefunded;
            $saleReturn->refund_due = $saleReturn->total_return_amount - $totalRefunded;

            if ($saleReturn->refund_due == 0) {
                $saleReturn->refund_status = "REFUNDED";
            } elseif ($totalRefunded > 0) {
                $saleReturn->refund_status = "PARTIAL";
            } else {
                $saleReturn->refund_status = "NOT PAID";
            }

            $saleReturn->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function invoicePrint($id)
    {
        $sale = Sales::with(['customer', 'items.product'])->findOrFail($id);
        $company = CompanyDetails::where('user_id', auth()->id())->first();
        return compact('sale', 'company');
    }


    public function returnInvoicePrint($id)
    {
        $saleReturn = SaleReturn::with(['customer', 'items.product'])->findOrFail($id);
        $company = CompanyDetails::where('user_id', auth()->id())->first();
        return compact('saleReturn', 'company');
    }


    public function returnInvoicePDF($id)
    {
        $saleReturn = SaleReturn::with(['customer', 'items.product', 'sale'])->findOrFail($id);
        $company = CompanyDetails::where('user_id', auth()->id())->first();

        $cleanRef = preg_replace('/[\/\\\\]/', '-', $saleReturn->return_no);
        $fileName = 'ReturnInvoice_' . $cleanRef . '.pdf';

        $logo = null;
        if ($company->company_logo && file_exists(public_path($company->company_logo))) {
            $logo = base64_encode(file_get_contents(public_path($company->company_logo)));
        }

        return [
            'saleReturn' => $saleReturn,
            'company'    => $company,
            'fileName'   => $fileName,
            'logo'       => $logo
        ];
    }


    /*******************************SALE REPORTS LOGIC *************************************************/
    public function saleReportData(array $data)
    {
        $query = Sales::with('customer')->where('user_id', auth()->id());

        if (!empty($data['from_date']) && !empty($data['to_date'])) {
            $fromDate = Carbon::parse($data['from_date'])->format('Y-m-d');
            $toDate = Carbon::parse($data['to_date'])->format('Y-m-d');
            $query->whereBetween('sale_date', [
                $fromDate,
                $toDate
            ]);
        }

        if (!empty($data['customer'])) {
            $query->whereIn('customer_id', $data['customer']);
        }

        $sales = $query->latest()->get();

        $summary = [
            'total' => $sales->sum('total_amount'),
            'paid'  => $sales->sum('paid_amount'),
            'due'   => $sales->sum('due_amount'),
        ];

        return [
            'sales'   => $sales,
            'filters' => $data,
            'summary' => $summary
        ];
    }


    public function itemReportData(array $data, $isExport = false)
    {
        $from = Carbon::createFromFormat('d-m-Y', $data['from_date'])->format('Y-m-d');
        $to   = Carbon::createFromFormat('d-m-Y', $data['to_date'])->format('Y-m-d');

        $query = SaleItem::with([
            'sale.customer',
            'product.brand'
        ])
            ->whereHas('sale', function ($q) use ($from, $to, $data) {
                $q->where('user_id', auth()->id())
                    ->whereBetween('sale_date', [$from, $to]);

                if (!empty($data['customer'])) {
                    $q->where('customer_id', $data['customer']);
                }
            });

        if (!empty($data['item_id'])) {
            $query->where('product_id', $data['item_id']);
        }

        if (!empty($data['brand_id'])) {
            $query->whereHas('product', function ($q) use ($data) {
                $q->where('brand_id', $data['brand_id']);
            });
        }

        $totalQuery = clone $query;

        $items = $isExport
            ? $query->latest()->get()        // export → full data
            : $query->latest()->get();

        $totals = [
            'qty'      => $totalQuery->sum('quantity'),
            'tax'      => $totalQuery->sum('tax_amount'),
            'discount' => $totalQuery->sum('discount'),
            'subtotal' => $totalQuery->sum('subtotal'),
        ];

        return [
            'items'   => $items,
            'filters' => $data,
            'totals'  => $totals,
        ];
    }



    public function salePaymentReportData($data, $isExport = false)
    {
        $from = Carbon::createFromFormat('d-m-Y', $data['from_date'])->format('Y-m-d');
        $to   = Carbon::createFromFormat('d-m-Y', $data['to_date'])->format('Y-m-d');

        $query = Sales::with('customer')
            ->where('user_id', auth()->id())
            ->whereBetween('sale_date', [$from, $to])
            ->where('paid_amount', '>', 0);


        if (!empty($data['customer'])) {
            $query->where('customer_id', $data['customer']);
        }


        if (!empty($data['payment_type'])) {
            $query->where('payment_method', $data['payment_type']);
        }


        $items = $isExport
            ? $query->latest()->get()
            : $query->latest()->limit(200)->get();

        return [
            'items' => $items,
            'filters' => $data
        ];
    }
    /*******************************SALE REPORTS LOGIC *************************************************/
}
