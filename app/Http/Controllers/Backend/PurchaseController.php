<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Services\PurchaseService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Services\AI\AIPurchaseHelper;

class PurchaseController extends Controller
{

    protected $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }


    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = $this->purchaseService->dataTable(auth()->id());

            return DataTables::of($data)

                ->editColumn('reference_no', function ($row) {
                    return '<code>' . $row->reference_no . '</code>';
                })

                ->editColumn('supplier', function ($row) {
                    return $row->supplier?->first_name . ' ' . $row->supplier?->last_name;
                })

                ->editColumn('created_by', function ($row) {
                    return $row->creator?->name ?? '';
                })

                ->editColumn('created_at', function ($row) {
                    return formatDate($row->created_at);
                })

                ->editColumn('purchase_date', function ($row) {
                    return formatDate($row->purchase_date); // d-m-Y (frontend)
                })

                ->addColumn('action', function ($row) {

                    $btn = '<div class="dropdown position-static">
                        <button class="p-0 px-1" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical fs-5"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow">';

                    $btn .= '<li>
                                <a class="dropdown-item" href="' . route('purchase.edit', $row->id) . '">
                                    <i class="bi bi-pencil-square me-2"></i> Edit
                                </a>
                     </li>';

                    $btn .= '<li>
                                <a class="dropdown-item" href="' . route('purchase.return.create', $row->id) . '">
                                    <i class="bi bi-arrow-left-right me-2"></i> Convert to Return
                                </a>
                     </li>';

                    $btn .= '<li>
                                <a class="dropdown-item" href="' . route('purchase.show', $row->id) . '">
                                    <i class="bi bi-eye me-2"></i> Details
                                </a>
                     </li>';

                    $btn .= '<li>
                                <a class="dropdown-item makePaymentBtn" href="javascript:void(0)" data-id=' . $row->id . '>
                                    <i class="bi bi-credit-card-2-back me-2"></i> Make Payment
                                </a>
                     </li>';

                    $btn .= '<li>
                        <a class="dropdown-item openHistory" href="javascript:void(0)" data-id=' . $row->id . '>
                            <i class="bi bi-list-check me-2"></i> Payment History
                        </a>
                     </li>';

                    $btn .= '<li>
                        <button class="dropdown-item purchase-delete" data-id="' . $row->id . '">
                            <i class="bi bi-trash me-2"></i> Delete
                        </button>
                     </li>';

                    $btn .= '</ul></div>';

                    return $btn;
                })

                ->rawColumns(['reference_no', 'action'])
                ->make(true);
        }

        return view('backend.purchase.index');
    }


    public function create()
    {
        $data = $this->purchaseService->master_parms(auth()->id());

        // Free AI Suggestion
        $ai = new AIPurchaseHelper();
        $data['aiSuggestions'] = $ai->getSuggestions();  // <-- IMPORTANT

        return view('backend.purchase.create', $data);
    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'supplier_id' => 'required',
                'purchase_date' => 'required|date',
                'bill_no' => 'required',

                'product_id' => 'required|array',
                'unit_cost'  => 'required|array',
                'quantity'   => 'required|array',

                'product_id.*' => 'required',
                'unit_cost.*'  => 'required|numeric',
                'quantity.*'   => 'required|numeric',

                'paid_amount' => 'required',
            ], [
                'product_id.required' => 'Please add at least one product.',
                'product_id.*.required' => 'Product is required.',
                'unit_cost.*.required' => 'Unit cost is required.',
                'quantity.*.required' => 'Quantity is required.',
                'paid_amount.required' => 'Paid amount is required.',
            ]);

            $userId = auth()->id();

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = [
                'user_id'         => $userId,
                'supplier_id'     => $request->supplier_id,
                'purchase_date'   => $request->purchase_date,
                'bill_no'         => $request->bill_no,
                'reference_no'    => $request->reference_no,
                'subtotal'        => $request->subtotal,
                'discount_type'   => $request->discount_type,
                // 'discount_amount' => $request->discount_amount,
                'tax_amount'      => $request->tax_amount,
                'shipping_charges' => $request->shipping_charges,
                'rounding'        => $request->rounding,
                'total_amount'    => $request->total_amount,
                'paid_amount'     => $request->paid_amount,
                'due_amount'      => $request->due_amount,
                'payment_method'  => $request->payment_method,
                'notes'           => $request->notes,
                'attachment'      => $request->attachment,  // if file, handle separately
                'created_by'      => $userId,

                // items
                'product_id'      => $request->product_id,
                'unit_cost'       => $request->unit_cost,
                'quantity'        => $request->quantity,
                'discount'        => $request->discount,
                'discount_type'   => $request->discount_type,
                'gst_percent'     => $request->gst_percent,
                'gst_amount'      => $request->gst_amount,
                'total'           => $request->total,
            ];

            $purchase = $this->purchaseService->purchaseSaved($data);

            // return redirect()->route('purchase.show', $purchase->id)->with('success', 'Purchase saved successfully!');

            return response()->json([
                'success' => true,
                'message' => 'Purchase saved successfully!',
                'redirect' => route('purchase.show', $purchase->id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function show($id)
    {
        $purchase = $this->purchaseService->purchaseShow($id);
        $data = $this->purchaseService->master_parms(auth()->id());
        return view('backend.purchase.show', compact('purchase'), $data);
    }


    public function edit($id)
    {
        $purchase = $this->purchaseService->purchaseEdit($id);
        $data = $this->purchaseService->master_parms(auth()->id());
        return view('backend.purchase.edit', compact('purchase'), $data);
    }


    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'supplier_id' => 'required',
                'purchase_date' => 'required|date',
                'bill_no' => 'required',

                'product_id' => 'required|array',
                'unit_cost'  => 'required|array',
                'quantity'   => 'required|array',

                'product_id.*' => 'required',
                'unit_cost.*'  => 'required|numeric',
                'quantity.*'   => 'required|numeric',

                'paid_amount' => 'required',
            ]);

            $userId = auth()->id();

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = [
                'user_id'         => $userId,
                'supplier_id'     => $request->supplier_id,
                'purchase_date'   => $request->purchase_date,
                'bill_no'         => $request->bill_no,
                'subtotal'        => $request->subtotal,
                'discount_type'   => $request->discount_type,
                'discount_amount' => $request->discount_amount,
                'tax_amount'      => $request->tax_amount,
                'shipping_charges' => $request->shipping_charges,
                'rounding'        => $request->rounding,
                'total_amount'    => $request->total_amount,
                'paid_amount'     => $request->paid_amount,
                'due_amount'      => $request->due_amount,
                'payment_method'  => $request->payment_method,
                'notes'           => $request->notes,
                'attachment'      => $request->attachment,  // if file, handle separately
                'created_by'      => $userId,

                // items
                'product_id'      => $request->product_id,
                'unit_cost'       => $request->unit_cost,
                'quantity'        => $request->quantity,
                'discount'        => $request->discount,
                'discount_type'   => $request->discount_type,
                'gst_percent'     => $request->gst_percent,
                'gst_amount'      => $request->gst_amount,
                'total'           => $request->total,
            ];

            $purchase = $this->purchaseService->purchaseUpdate($data, $id);

            return response()->json([
                'success'  => true,
                'message'  => 'Purchase updated successfully!',
                'redirect' => route('purchase.show', $purchase->id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function getPurchaseData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:purchases,id',
            ]);


            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }


            $purchase = $this->purchaseService->Get_purchase_data($validator->validate());

            return response()->json([
                'success'  => true,
                'data'  => $purchase,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function makePayment(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount'         => 'required|numeric|min:1',
                'payment_method' => 'required|not_in:0',
                'payment_date'   => 'required|date',
                'note'           => 'nullable|string',
            ]);


            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validate();

            $paymentSuccess = $this->purchaseService->savePayment($data, $id);

            if (!$paymentSuccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment cannot exceed due amount.'
                ], 422);
            }

            return response()->json([
                'success'  => true,
                'message'  => 'Payment recorded successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function paymentHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchaseId' => 'required|exists:purchases,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $purchase = Purchase::with(['supplier', 'payments'])
            ->find($request->purchaseId);

        return response()->json([
            'success'   => true,
            'data'      => $purchase,
            'payments'  => $purchase->payments,
            'total_paid' => $purchase->paid_amount,
            'due_amount' => $purchase->due_amount,
            'bill_no'   => $purchase->bill_no,
        ]);
    }


    public function purchasePaymentDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|exists:party_transactions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $this->purchaseService->deletePayment($validator->validate());

            return response()->json([
                'success' => true,
                'message' => "Purchase payment entry deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchase_id' => 'required|exists:purchases,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $this->purchaseService->purchaseDelete($validator->validate());

            return response()->json([
                'success' => true,
                'message' => 'Purchase deleted successfully.'
            ], 200);
        } catch (\Exception $e) {

            // SERVICE error ko yahin catch karke JSON me bhejenge
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function printInvoice($id)
    {
        $data = $this->purchaseService->purchasePrintInvoice($id);
        return view('backend.purchase.print', $data);
    }

    public function GeneratePdf($id)
    {
        $data = $this->purchaseService->purchaseInvoicePDF($id);

        return Pdf::loadView('backend.purchase.purchaseinvoice', $data)
            ->setPaper('A4', 'portrait')
            ->set_option('isHtml5ParserEnabled', true)
            ->set_option('isRemoteEnabled', true)
            ->download($data['fileName']);
    }
}
