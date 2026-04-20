<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PurchaseReturn;
use App\Services\PurchasereturnService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PurchaseReturnController extends Controller
{
    protected $purchaseReturnService;

    public function __construct(PurchasereturnService $purchaseReturnService)
    {
        $this->purchaseReturnService = $purchaseReturnService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = $this->purchaseReturnService->listData(auth()->id());

            return DataTables::of($data)

                ->editColumn('return_no', function ($row) {
                    return '<code>' . $row->return_no . '</code>';
                })

                ->editColumn('supplier', function ($row) {
                    return $row->supplier?->first_name . ' ' . $row->supplier?->last_name;
                })

                ->editColumn('creator', function ($row) {
                    return $row->creator?->name ?? '';
                })

                ->editColumn('created_at', function ($row) {
                    return formatDate($row->created_at);
                })

                ->editColumn('return_date', function ($row) {
                    return formatDate($row->return_date);
                })


                ->addColumn('action', function ($row) {

                    $btn = '<div class="dropdown position-static">
                            <button class="p-0 px-1" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical fs-5"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">';

                    $btn .= '<li>
                                <a class="dropdown-item" href="' . route('purchase.return.edit', $row->id) . '">
                                    <i class="bi bi-pencil-square me-2"></i> Edit
                                </a>
                            </li>';

                    $btn .= '<li>
                                <a class="dropdown-item" href="' . route('purchase.return.show', $row->id) . '">
                                    <i class="bi bi-eye me-2"></i> Details
                                </a>
                            </li>';

                    $btn .= '<li>
                                    <a class="dropdown-item return-receive-payment"
                                    href="javascript:void(0)"
                                    data-id="' . $row->id . '">
                                    <i class="bi bi-credit-card-2-back me-2"></i> Receive Payment
                                    </a>
                                </li>';

                    $btn .= '<li>
                                <a class="dropdown-item receivedHistory" href="javascript:void(0)" data-id=' . $row->id . '>
                                    <i class="bi bi-list me-2"></i> Payment History
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

                ->rawColumns(['return_no', 'action'])
                ->make(true);
        }
        return view('backend.purchasereturn.index');
    }

    public function create($purchase_id)
    {
        $data = $this->purchaseReturnService->purms($purchase_id);
        return view('backend.purchasereturn.create', $data);
    }

    public function store(Request $request, $id)
    {
        $validator = Validator::make(
            array_merge(
                $request->all(),
                ['purchase_id' => $id]
            ),
            [
                'purchase_id'   => 'required|exists:purchases,id',
                'return_date'   => 'required',
                'return_qty'    => 'required|array',
                'return_qty.*'  => 'numeric|min:1',
                'note'          => 'nullable'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validate();

            $this->purchaseReturnService->storeReturn($data, $id);

            return response()->json([
                'success' => true,
                'message' => 'Purchase return completed successfully.'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function edit($preturn_id)
    {
        $pr = $this->purchaseReturnService->EditPurchaseReturn($preturn_id);

        return view('backend.purchasereturn.edit', [
            'purchaseReturn' => $pr,
            'purchase'       => $pr->purchase,
            'items'          => $pr->items
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->purchaseReturnService->updateReturn($request->all(), $id);
        return redirect()->route('purchase.return.index')->with('success', 'Purchase return updated successfully');
    }


    public function show(string $id)
    {
        $data = $this->purchaseReturnService->purchaseReturnShow($id);
        return view('backend.purchasereturn.show', $data);
    }


    public function GetPurchaseReturnData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'returnId' => 'required|exists:purchase_returns,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $purchaseReturn = $this->purchaseReturnService->DataGetPurchaseReturn($validator->validate());

        return response()->json([
            'success' => true,
            'data'    => $purchaseReturn
        ]);
    }

    public function purchaseReturnPayment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|in:Cash,UPI,Bank,Cheque',
            'payment_date'   => 'required|date',
            'note'           => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $purchaseReturn = $this->purchaseReturnService->saveReturnPayment(
                $validator->validate(),
                $id // ← Only this ID is used
            );

            return response()->json([
                'success'  => true,
                'message'  => "Refund saved successfully!",
                'redirect' => route('purchase.return.show', $purchaseReturn->id),
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function purchaseReturnHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchase_return_id' => 'required|exists:purchase_returns,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $this->purchaseReturnService->returnPaymentHistory($validator->validate());

        return response()->json([
            'success' => true,
            'data'    => $data
        ]);
    }

    public function deleteReturnPayment(Request $request)
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
            $this->purchaseReturnService->deleteReturnPayment($validator->validate());

            return response()->json([
                'success' => true,
                'message' => "Refund entry deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function printInvoice($id)
    {
        $data = $this->purchaseReturnService->returnPrintInvoive($id);
        return view('backend.purchasereturn.print', $data);
    }


    public function invoicePdf($id)
    {
        $data = $this->purchaseReturnService->purchaseReturnInvoicePDF($id);

        return Pdf::loadView('backend.purchasereturn.returninvoice', $data)
            ->setPaper('A4', 'portrait')
            ->set_option('isHtml5ParserEnabled', true)
            ->set_option('isRemoteEnabled', true)
            ->download($data['fileName']);
    }
}
