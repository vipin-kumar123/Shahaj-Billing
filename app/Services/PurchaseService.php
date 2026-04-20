<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\CompanyDetails;
use App\Models\Customer;
use App\Models\PartyTransaction;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\SupplierLedgers;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function master_parms($userId)
    {
        return [
            'products' => Product::where('is_active', 1)->where('user_id', $userId)->get(),
            'brands' => Brand::where('is_active', 1)->where('user_id', $userId)->get(),
            'suppliers' => Customer::where('type', 'supplier')->where('is_active', 1)->where('user_id', $userId)->get(),
            'company'   => CompanyDetails::with(['state', 'city'])->where('user_id', $userId)->first()
        ];
    }

    public function dataTable($userId)
    {
        return Purchase::with('supplier', 'creator')->latest()->where('user_id', $userId);
    }

    public function purchaseSaved(array $data)
    {
        DB::beginTransaction();

        try {

            /*
        |--------------------------------------------------------------------------
        | CREATE PURCHASE (WITHOUT TRUSTING DUE)
        |--------------------------------------------------------------------------
        */

            $purchase = Purchase::create([
                'user_id'          => auth()->id(),
                'supplier_id'      => $data['supplier_id'],
                'purchase_date'    => $data['purchase_date'],
                'bill_no'          => $data['bill_no'],
                'reference_no'     => $data['reference_no'] ?? null,

                'payment_status'   => 'DUE', // temporary

                'subtotal'         => $data['subtotal'],
                'discount_type'    => null,
                'discount_amount'  => 0,
                'tax_amount'       => $data['tax_amount'],
                'shipping_charges' => $data['shipping_charges'],
                'rounding'         => $data['rounding'],
                'total_amount'     => $data['total_amount'],

                'paid_amount'      => 0, // will recalc
                'due_amount'       => $data['total_amount'], // initial full due

                'payment_method'   => $data['payment_method'] ?? null,
                'notes'            => $data['notes'] ?? null,
                'attachment'       => '',

                'created_by'       => auth()->id(),
            ]);


            /*
        |--------------------------------------------------------------------------
        | HANDLE ATTACHMENT
        |--------------------------------------------------------------------------
        */

            if (!empty($data['attachment']) && $data['attachment']->isValid()) {

                $folder = "assets/backend/uploads/purchase/attachment/" . $purchase->id . "/";

                if (!file_exists(public_path($folder))) {
                    mkdir(public_path($folder), 0777, true);
                }

                $file = $data['attachment'];
                $filename = time() . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($folder), $filename);

                $purchase->update([
                    'attachment' => $folder . $filename
                ]);
            }


            /*
        |--------------------------------------------------------------------------
        | SAVE ITEMS + STOCK MOVEMENT
        |--------------------------------------------------------------------------
        */

            foreach ($data['product_id'] as $i => $productId) {

                PurchaseItem::create([
                    'purchase_id'   => $purchase->id,
                    'product_id'    => $productId,
                    'unit_id'       => 1,
                    'unit_cost'     => $data['unit_cost'][$i],
                    'quantity'      => $data['quantity'][$i],
                    'discount'      => $data['discount'][$i] ?? 0,
                    'discount_type' => $data['discount_type'][$i] ?? null,
                    'gst_percent'   => $data['gst_percent'][$i] ?? 0,
                    'gst_amount'    => $data['gst_amount'][$i] ?? 0,
                    'total'         => $data['total'][$i] ?? 0,
                ]);

                StockMovement::create([
                    'product_id'  => $productId,
                    'user_id'     => auth()->id(),
                    'purchase_id' => $purchase->id,
                    'type'        => 'increase',
                    'quantity'    => $data['quantity'][$i],
                ]);
            }


            /*
        |--------------------------------------------------------------------------
        | SUPPLIER LEDGER ENTRY (PURCHASE LIABILITY)
        |--------------------------------------------------------------------------
        */

            SupplierLedgers::create([
                'user_id'     => auth()->id(),
                'supplier_id' => $data['supplier_id'],
                'purchase_id' => $purchase->id,
                'type'        => 'purchase',
                'amount'      => $purchase->total_amount,
                'note'        => 'Purchase Bill #' . $purchase->bill_no,
            ]);


            /*
        |--------------------------------------------------------------------------
        | INITIAL PAYMENT (IF ANY)
        |--------------------------------------------------------------------------
        */

            if (!empty($data['paid_amount']) && $data['paid_amount'] > 0) {

                PartyTransaction::create([
                    'user_id'        => auth()->id(),
                    'party_id'       => $purchase->supplier_id,
                    'reference_type' => 'purchase',
                    'reference_id'   => $purchase->id,
                    'type'           => 'debit', // money paid to supplier
                    'amount'         => $data['paid_amount'],
                    'payment_method' => $data['payment_method'] ?? 'cash',
                    'payment_date'   => $data['purchase_date'],
                    'note'           => 'Initial payment for Purchase #' . $purchase->bill_no,
                ]);

                SupplierLedgers::create([
                    'user_id'     => auth()->id(),
                    'supplier_id' => $purchase->supplier_id,
                    'purchase_id' => $purchase->id,
                    'type'        => 'payment',
                    'amount'      => $data['paid_amount'],
                    'note'        => 'Payment on Purchase #' . $purchase->bill_no,
                ]);
            }


            /*
        |--------------------------------------------------------------------------
        | RECALCULATE PAID & DUE (SINGLE SOURCE OF TRUTH)
        |--------------------------------------------------------------------------
        */

            $totalPaid = PartyTransaction::where('reference_type', 'purchase')
                ->where('reference_id', $purchase->id)
                ->sum('amount');

            $purchase->paid_amount = $totalPaid;
            $purchase->due_amount  = $purchase->total_amount - $totalPaid;

            if ($purchase->due_amount <= 0) {
                $purchase->payment_status = 'PAID';
                $purchase->due_amount = 0;
            } elseif ($totalPaid > 0) {
                $purchase->payment_status = 'PARTIAL';
            } else {
                $purchase->payment_status = 'DUE';
            }

            $purchase->save();

            DB::commit();
            return $purchase;
        } catch (\Exception $e) {

            DB::rollBack();
            throw $e;
        }
    }



    public function purchaseShow($id)
    {
        return Purchase::with([
            'supplier',
            'items.product'
        ])->findOrFail($id);
    }

    public function purchaseEdit($id)
    {
        return Purchase::with([
            'supplier',
            'items.product'
        ])->findOrFail($id);
    }


    public function purchaseUpdate(array $data, $id)
    {
        DB::beginTransaction();

        try {

            $purchase = Purchase::findOrFail($id);

            /*
        |--------------------------------------------------------------------------
        | ATTACHMENT HANDLE
        |--------------------------------------------------------------------------
        */

            $attachmentPath = $purchase->attachment;

            if (!empty($data['attachment']) && $data['attachment']->isValid()) {

                if ($purchase->attachment && file_exists(public_path($purchase->attachment))) {
                    @unlink(public_path($purchase->attachment));
                }

                $folder = "assets/backend/uploads/purchase/attachment/" . $purchase->id . "/";
                if (!file_exists(public_path($folder))) {
                    mkdir(public_path($folder), 0777, true);
                }

                $file = $data['attachment'];
                $filename = time() . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($folder), $filename);

                $attachmentPath = $folder . $filename;
            }

            /*
        |--------------------------------------------------------------------------
        | UPDATE PURCHASE HEADER (WITHOUT TOUCHING PAYMENT)
        |--------------------------------------------------------------------------
        */

            $purchase->update([
                'supplier_id'      => $data['supplier_id'],
                'purchase_date'    => $data['purchase_date'],
                'bill_no'          => $data['bill_no'],
                'subtotal'         => $data['subtotal'],
                'tax_amount'       => $data['tax_amount'],
                'shipping_charges' => $data['shipping_charges'],
                'rounding'         => $data['rounding'],
                'total_amount'     => $data['total_amount'],
                'notes'            => $data['notes'],
                'attachment'       => $attachmentPath,
            ]);


            /*
        |--------------------------------------------------------------------------
        | STOCK REVERSAL (VERY IMPORTANT)
        |--------------------------------------------------------------------------
        */

            // Reverse old stock
            $oldItems = PurchaseItem::where('purchase_id', $purchase->id)->get();

            foreach ($oldItems as $oldItem) {
                StockMovement::create([
                    'product_id'  => $oldItem->product_id,
                    'user_id'     => auth()->id(),
                    'purchase_id' => $purchase->id,
                    'type'        => 'decrease', // reverse previous increase
                    'quantity'    => $oldItem->quantity,
                ]);
            }

            // Delete old items
            PurchaseItem::where('purchase_id', $purchase->id)->delete();


            /*
        |--------------------------------------------------------------------------
        | ADD NEW ITEMS + STOCK INCREASE
        |--------------------------------------------------------------------------
        */

            foreach ($data['product_id'] as $i => $productId) {

                PurchaseItem::create([
                    'purchase_id'   => $purchase->id,
                    'product_id'    => $productId,
                    'unit_id'       => 1,
                    'unit_cost'     => $data['unit_cost'][$i],
                    'quantity'      => $data['quantity'][$i],
                    'discount'      => $data['discount'][$i] ?? 0,
                    'discount_type' => $data['discount_type'][$i] ?? null,
                    'gst_percent'   => $data['gst_percent'][$i] ?? 0,
                    'gst_amount'    => $data['gst_amount'][$i] ?? 0,
                    'total'         => $data['total'][$i] ?? 0,
                ]);

                StockMovement::create([
                    'product_id'  => $productId,
                    'user_id'     => auth()->id(),
                    'purchase_id' => $purchase->id,
                    'type'        => 'increase',
                    'quantity'    => $data['quantity'][$i],
                ]);
            }


            /*
        |--------------------------------------------------------------------------
        |UPDATE SUPPLIER LEDGER (ONLY PURCHASE ENTRY)
        |--------------------------------------------------------------------------
        */

            // Delete only purchase type ledger (NOT payment)
            SupplierLedgers::where('purchase_id', $purchase->id)
                ->where('type', 'purchase')
                ->delete();

            SupplierLedgers::create([
                'user_id'     => auth()->id(),
                'supplier_id' => $purchase->supplier_id,
                'purchase_id' => $purchase->id,
                'type'        => 'purchase',
                'amount'      => $purchase->total_amount,
                'note'        => 'Updated Purchase #' . $purchase->bill_no,
            ]);


            /*
        |--------------------------------------------------------------------------
        | RECALCULATE PAYMENT (DON'T TRUST FRONTEND)
        |--------------------------------------------------------------------------
        */

            $totalPaid = PartyTransaction::where('reference_type', 'purchase')
                ->where('reference_id', $purchase->id)
                ->sum('amount');

            $purchase->paid_amount = $totalPaid;
            $purchase->due_amount  = $purchase->total_amount - $totalPaid;

            if ($purchase->due_amount <= 0) {
                $purchase->payment_status = 'PAID';
                $purchase->due_amount = 0;
            } elseif ($totalPaid > 0) {
                $purchase->payment_status = 'PARTIAL';
            } else {
                $purchase->payment_status = 'DUE';
            }

            $purchase->save();


            DB::commit();
            return $purchase;
        } catch (\Exception $e) {

            DB::rollBack();
            throw $e;
        }
    }



    public function Get_purchase_data(array $data)
    {
        return Purchase::with(['supplier'])->findOrFail($data['id']);
    }

    public function savePayment(array $data, $id)
    {
        DB::beginTransaction();

        try {

            $purchase = Purchase::findOrFail($id);

            //Recalculate correct due dynamically
            $totalReturned = PurchaseReturn::where('purchase_id', $purchase->id)
                ->sum('total_return_amount');

            $netPurchase = $purchase->total_amount - $totalReturned;

            $currentDue = $netPurchase - $purchase->paid_amount;

            //If already overpaid, block payment
            if ($currentDue <= 0) {
                DB::rollBack();
                throw new \Exception("This purchase is already fully settled or in advance.");
            }

            //Prevent overpayment
            if ($data['amount'] > $currentDue) {
                DB::rollBack();
                throw new \Exception("Payment amount exceeds current due.");
            }

            //Save transaction
            PartyTransaction::create([
                'user_id'        => auth()->id(),
                'party_id'       => $purchase->supplier_id,
                'reference_type' => 'purchase',
                'reference_id'   => $purchase->id,
                'type'           => 'debit',
                'amount'         => $data['amount'],
                'payment_method' => $data['payment_method'],
                'payment_date'   => Carbon::createFromFormat('d-m-Y', $data['payment_date'])->format('Y-m-d'),
                'note'           => 'Payment for Purchase #' . $purchase->bill_no,
            ]);

            // Update paid amount
            $purchase->paid_amount += $data['amount'];

            //Recalculate due again
            $purchase->due_amount = $netPurchase - $purchase->paid_amount;

            // Update payment status
            if ($purchase->due_amount == 0) {
                $purchase->payment_status = "PAID";
            } elseif ($purchase->paid_amount > 0) {
                $purchase->payment_status = "PARTIAL";
            }

            $purchase->save();

            // Ledger entry
            SupplierLedgers::create([
                'user_id'     => auth()->id(),
                'supplier_id' => $purchase->supplier_id,
                'purchase_id' => $purchase->id,
                'type'        => 'payment',
                'amount'      => $data['amount'],
                'note'        => "Payment against Bill #" . $purchase->bill_no,
            ]);

            DB::commit();

            return true;
        } catch (\Exception $e) {

            DB::rollBack();
            throw $e;
        }
    }

    public function deletePayment(array $data)
    {
        DB::beginTransaction();

        try {
            // 1) Find transaction
            $transaction = PartyTransaction::findOrFail($data['payment_id']);

            // Ensure this is purchase payment
            if ($transaction->reference_type !== "purchase") {
                throw new \Exception("Invalid purchase payment transaction type.");
            }

            // 2) Find the purchase
            $purchase = Purchase::findOrFail($transaction->reference_id);

            // 3) Delete the transaction
            $transaction->delete();

            SupplierLedgers::where([
                'purchase_id' => $purchase->id,
                'type' => 'payment',
                'amount' => $transaction->amount
            ])->latest()->first()?->delete();

            // 4) Recalculate paid amount again from PartyTransaction
            $totalPaid = PartyTransaction::where('reference_type', 'purchase')
                ->where('reference_id', $purchase->id)
                ->sum('amount');

            // 5) Also subtract purchase returns
            $totalReturned = PurchaseReturn::where('purchase_id', $purchase->id)
                ->sum('total_return_amount');

            // Net purchase = total - returns
            $netPurchase = $purchase->total_amount - $totalReturned;

            // 6) Update purchase amounts
            $purchase->paid_amount = $totalPaid;
            $purchase->due_amount = $netPurchase - $totalPaid;

            // 7) Update payment status
            if ($purchase->due_amount == 0) {
                $purchase->payment_status = 'PAID';
            } elseif ($purchase->paid_amount > 0) {
                $purchase->payment_status = 'PARTIAL';
            } else {
                $purchase->payment_status = 'UNPAID';
            }

            $purchase->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function purchaseDelete(array $data)
    {
        DB::beginTransaction();

        try {
            $purchase = Purchase::with(['items.product', 'returns.items'])->findOrFail($data['purchase_id']);

            //Step 0: STOP delete if any purchase item was sold
            foreach ($purchase->items as $pItem) {

                $usedInSale = SaleItem::where('product_id', $pItem->product_id)
                    ->where('quantity', '>', 0)
                    ->exists();

                if ($usedInSale) {
                    throw new \Exception(
                        "Cannot delete purchase. Product '{$pItem->product->name}' is already used in sales."
                    );
                }
            }

            // ------------------------------------
            // Step 1: Reverse purchase stock (IN → OUT)
            // ------------------------------------
            foreach ($purchase->items as $pItem) {
                StockMovement::create([
                    'product_id'  => $pItem->product_id,
                    'user_id'     => auth()->id(),
                    'purchase_id' => $purchase->id,
                    'type'        => 'decrease',
                    'quantity'    => $pItem->quantity,
                ]);
            }

            // ------------------------------------
            // Step 2: Reverse return stock (OUT → IN)
            // ------------------------------------
            foreach ($purchase->returns as $return) {

                foreach ($return->items as $rItem) {
                    StockMovement::create([
                        'product_id'  => $rItem->product_id,
                        'user_id'     => auth()->id(),
                        'purchase_id' => $purchase->id,
                        'type'        => 'increase',
                        'quantity'    => $rItem->return_qty,
                    ]);
                }

                PurchaseReturnItem::where('purchase_return_id', $return->id)->delete();
            }

            PurchaseReturn::where('purchase_id', $purchase->id)->delete();
            PurchaseItem::where('purchase_id', $purchase->id)->delete();
            StockMovement::where('purchase_id', $purchase->id)->delete();
            SupplierLedgers::where('purchase_id', $purchase->id)->delete();
            PartyTransaction::where('reference_id', $purchase->id)->delete();

            if ($purchase->attachment && file_exists(public_path($purchase->attachment))) {
                @unlink(public_path($purchase->attachment));
            }

            $purchase->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function purchasePrintInvoice($id)
    {
        $purchase = Purchase::with(['supplier', 'items.product'])->findOrFail($id);
        $company = CompanyDetails::where('user_id', auth()->id())->first();
        return compact('purchase', 'company');
    }

    public function purchaseInvoicePDF($id)
    {
        $purchase = Purchase::with(['supplier', 'items.product'])->findOrFail($id);
        $company = CompanyDetails::where('user_id', auth()->id())->first();

        $cleanRef = preg_replace('/[\/\\\\]/', '-', $purchase->reference_no);
        $fileName = 'PurchaseInvoice_' . $cleanRef . '.pdf';

        $logo = null;
        if ($company->company_logo && file_exists(public_path($company->company_logo))) {
            $logo = base64_encode(file_get_contents(public_path($company->company_logo)));
        }

        return [
            'purchase' => $purchase,
            'company'    => $company,
            'fileName'   => $fileName,
            'logo'       => $logo
        ];
    }

    /*********************PURCHASE REPORT START****************************************/

    public function reportPurchaseData(array $data, $isExport = false)
    {
        $from = Carbon::createFromFormat('d-m-Y', $data['from_date'])->format('Y-m-d');
        $to   = Carbon::createFromFormat('d-m-Y', $data['to_date'])->format('Y-m-d');

        $query = Purchase::with('supplier')
            ->whereBetween('purchase_date', [$from, $to]);

        if (!empty($data['supplier_id'])) {
            $query->where('supplier_id', $data['supplier_id']);
        }

        $purchases = $query->latest()->get();

        $items = $purchases->map(function ($purchase) {
            return [
                'purchase_date' => Carbon::parse($purchase->purchase_date)->format('d-m-Y'),
                'bill_no' => $purchase->bill_no,
                'supplier' => $purchase->supplier->first_name . ' ' . $purchase->supplier->last_name,
                'grand_total' => $purchase->total_amount,
                'paid_amount' => $purchase->paid_amount,
                'balance' => $purchase->due_amount,
            ];
        });

        return [
            'items' => $items,
            'purchases' => $purchases
        ];
    }


    public function itemPurchaseReportData(array $data)
    {
        $from = Carbon::createFromFormat('d-m-Y', $data['from_date'])->format('Y-m-d');
        $to   = Carbon::createFromFormat('d-m-Y', $data['to_date'])->format('Y-m-d');

        $query = PurchaseItem::with([
            'purchase',
            'product',
            'product.brand',
            'purchase.supplier'
        ])
            ->whereHas('purchase', function ($q) use ($data, $from, $to) {

                $q->whereBetween('purchase_date', [$from, $to]);

                if (!empty($data['supplier_id'])) {
                    $q->where('supplier_id', $data['supplier_id']);
                }
            });

        if (!empty($data['product_id']) && is_numeric($data['product_id'])) {
            $query->where('product_id', (int)$data['product_id']);
        }

        if (!empty($data['brand_id']) && is_numeric($data['brand_id'])) {
            $query->whereHas('product.brand', function ($q) use ($data) {
                $q->where('id', (int)$data['brand_id']);
            });
        }

        $purchaseItems = $query->latest()->get();

        $items = $purchaseItems->map(function ($pi) {

            $purchase = $pi->purchase;

            return [
                'date'       => Carbon::parse($purchase->purchase_date)->format('d-m-Y'),
                'bill_no'    => $purchase->bill_no,

                'supplier'   => trim(
                    optional($purchase->supplier)->first_name . ' ' .
                        optional($purchase->supplier)->last_name
                ),

                'item'       => optional($pi->product)->name,
                'brand'      => optional(optional($pi->product)->brand)->name,

                'unit_price' => $pi->unit_cost,
                'quantity'   => $pi->quantity,

                'discount'   => $pi->discount ?? 0,
                'tax'        => $pi->gst_amount ?? 0,

                'total'      => $pi->total,
            ];
        });

        return [
            'items' => $items,
            'raw'   => $purchaseItems
        ];
    }


    public function purchasePaymentData(array $data)
    {
        $from = Carbon::createFromFormat('d-m-Y', $data['from_date'])->format('Y-m-d');
        $to   = Carbon::createFromFormat('d-m-Y', $data['to_date'])->format('Y-m-d');

        $query = Purchase::with('supplier')
            ->where('user_id', auth()->id())
            ->whereBetween('purchase_date', [$from, $to])
            ->where('paid_amount', '>', 0);

        if (!empty($data['supplier_id'])) {
            $query->where('supplier_id', $data['supplier_id']);
        }

        if (!empty($data['payment_type'])) {
            $query->where('payment_method', $data['payment_type']);
        }

        $result = $query->latest()->get();

        $items = $result->map(function ($purchase) {
            return [
                'date'        => Carbon::parse($purchase->purchase_date)->format('d-m-Y'),
                'bill_no'     => $purchase->bill_no,

                'supplier'    => trim(
                    optional($purchase->supplier)->first_name . ' ' .
                        optional($purchase->supplier)->last_name
                ),

                'payment_type' => $purchase->payment_method,
                'paid_amount' => $purchase->paid_amount,
            ];
        });
        // print_r($items);
        //exit;
        return [
            'items' => $items,
            'total_paid' => $result->sum('paid_amount'),
        ];
    }
}
