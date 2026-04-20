<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\SaleService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SaleReturnController extends Controller
{
    protected $saleservice;

    public function __construct(SaleService $saleservice)
    {
        $this->saleservice = $saleservice;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = $this->saleservice->returnDataTable();

            return DataTables::of($data)
                ->editColumn('return_no', function ($row) {
                    return '<code>' . $row->return_no . '</code>';
                })

                ->editColumn('customer', function ($row) {
                    return $row->customer?->first_name . ' ' . $row->customer?->last_name;
                })

                ->filterColumn('customer', function ($query, $keyword) {
                    $query->whereHas('customer', function ($q) use ($keyword) {
                        $q->where(function ($subQuery) use ($keyword) {
                            $subQuery->where('first_name', 'like', "%{$keyword}%")
                                ->orWhere('last_name', 'like', "%{$keyword}%");
                        });
                    });
                })

                ->editColumn('creator', function ($row) {
                    return $row->creator?->name ?? '';
                })

                ->filterColumn('creator', function ($query, $keyword) {
                    $query->whereHas('creator', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })

                ->editColumn('created_at', function ($row) {
                    return formatDate($row->created_at);
                })

                ->editColumn('return_date', function ($row) {
                    return formatDate($row->return_date);
                })

                ->filterColumn('return_date', function ($query, $keyword) {
                    try {
                        $date = \Carbon\Carbon::createFromFormat('d-m-Y', $keyword)
                            ->format('Y-m-d');

                        $query->whereDate('return_date', $date);
                    } catch (\Exception $e) {
                        // Invalid format ignore
                    }
                })


                ->addColumn('action', function ($row) {

                    $btn = '<div class="dropdown position-static">
                            <button class="p-0 px-1" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical fs-5"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">';

                    $btn .= '<li>
                                <a class="dropdown-item" href="' . route('sale.return.edit', $row->id) . '">
                                    <i class="bi bi-pencil-square me-2"></i> Edit
                                </a>
                            </li>';

                    $btn .= '<li>
                                <a class="dropdown-item" href="' . route('sale.return.show', $row->id) . '">
                                    <i class="bi bi-eye me-2"></i> Details
                                </a>
                            </li>';

                    $btn .= '<li>
                                <a class="dropdown-item refund-payment" href="javascript:void(0)" data-id="' . $row->id . '">
                                    <i class="bi bi-eye me-2"></i> Refund Payment
                                </a>
                            </li>';

                    $btn .= '<li>
                                <a class="dropdown-item refund-history" href="javascript:void(0)" data-id="' . $row->id . '">
                                    <i class="bi bi-eye me-2"></i> Refund History
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
        return view('backend.salereturn.index');
    }

    public function create(Request $request, $sale_id)
    {
        $data = $this->saleservice->saleData($sale_id);
        return view('backend.salereturn.create', $data);
    }

    public function store(Request $request, $sale_id)
    {
        $validator = Validator::make(
            array_merge($request->all(), ['sale_id' => $sale_id]),
            [
                'sale_id'       => 'required|exists:sales,id',
                'return_date'   => 'required|date',
                'return_qty'    => 'nullable|array',
                'return_qty.*'  => 'nullable|numeric|min:0',
                'note'          => 'nullable'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validate();

        try {
            $data = $validator->validate();

            $this->saleservice->storeSaleReturn($data, $sale_id);

            return response()->json([
                'success' => true,
                'message' => 'Sale return completed successfully.'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $data = $this->saleservice->editSaleReturn($id);
        return view('backend.salereturn.edit', $data);
    }


    public function update(Request $request, $returnId)
    {
        $this->saleservice->updateSaleReturn($request->all(), $returnId);
        return response()->json([
            'success' => true,
            'message' => 'Sale return updated successfully'
        ]);
        //return redirect()->route('purchase.return.index')->with('success', 'Sale return updated successfully');
    }


    public function show(string $id)
    {
        $data = $this->saleservice->returnShow($id);
        return view('backend.salereturn.show', $data);
    }


    public function GetSaleReturnData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sale_return_id' => 'required|exists:sale_returns,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $this->saleservice->refundUsedSaleReturnData($validator->validate());

        return response()->json([
            'success'  => true,
            'data'  => $data
        ]);
    }


    public function refundPayment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'sale_return_id' => 'required|exists:sale_returns,id',
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
            $data = $validator->validate();
            $result = $this->saleservice->saveRefundPayment($data, $id);

            return response()->json([
                'success'  => true,
                'message'  => 'Refund saved successfully!',
                'redirect' => route('sale.return.show', $result->id),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function refundHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sale_return_id' => 'required|exists:sale_returns,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $this->saleservice->saleReturnHistory($validator->validate());

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function deleteSaleReturnRefund(Request $request)
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
            $this->saleservice->deleteRefundPayment($validator->validate());

            return response()->json([
                'success' => true,
                'message' => "Refund payment deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function returnPrint($id)
    {
        $data = $this->saleservice->returnInvoicePrint($id);
        return view('backend.salereturn.print', $data);
    }


    public function GeneratePdf($id)
    {
        $data = $this->saleservice->returnInvoicePDF($id);

        return Pdf::loadView('backend.salereturn.returninvoice', $data)
            ->setPaper('A4', 'portrait')
            ->set_option('isHtml5ParserEnabled', true)
            ->set_option('isRemoteEnabled', true)
            ->download($data['fileName']);
    }
}
