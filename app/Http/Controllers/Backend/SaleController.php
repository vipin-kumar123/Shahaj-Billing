<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CompanyDetails;
use App\Models\Sales;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SaleController extends Controller
{

    protected $saleservice;

    public function __construct(SaleService $saleservice)
    {
        $this->saleservice = $saleservice;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = $this->saleservice->dataTable();

            return DataTables::of($data)

                ->editColumn('reference_no', function ($row) {
                    return '<code>' . $row->reference_no . '</code>';
                })

                ->addColumn('customer', function ($row) {
                    return $row->customer
                        ? $row->customer->first_name . ' ' . $row->customer->last_name
                        : '-';
                })
                ->filterColumn('customer', function ($query, $keyword) {
                    $query->whereHas('customer', function ($q) use ($keyword) {
                        $q->where(function ($subQuery) use ($keyword) {
                            $subQuery->where('first_name', 'like', "%{$keyword}%")
                                ->orWhere('last_name', 'like', "%{$keyword}%");
                        });
                    });
                })

                ->editColumn('created_by', function ($row) {
                    return $row->creator?->name ?? '';
                })

                ->editColumn('created_at', function ($row) {
                    return formatDate($row->created_at);
                })

                ->editColumn('sale_date', function ($row) {
                    return formatDate($row->sale_date); // d-m-Y (frontend)
                })

                ->filterColumn('sale_date', function ($query, $keyword) {
                    try {
                        $date = \Carbon\Carbon::createFromFormat('d-m-Y', $keyword)
                            ->format('Y-m-d');

                        $query->whereDate('sale_date', $date);
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
                                <a class="dropdown-item" href="' . route('sale.edit', $row->id) . '">
                                    <i class="bi bi-pencil-square me-2"></i> Edit
                                </a>
                     </li>';

                    $btn .= '<li>
                                <a class="dropdown-item" href="' . route('sale.return.convert', $row->id) . '">
                                    <i class="bi bi-arrow-left-right me-2"></i> Convert to Return
                                </a>
                     </li>';

                    $btn .= '<li>
                                <a class="dropdown-item" href="' . route('sale.show', $row->id) . '">
                                    <i class="bi bi-eye me-2"></i> Details
                                </a>
                     </li>';

                    $btn .= '<li>
                                <a class="dropdown-item receivePaymentBtn" href="javascript:void(0)" data-id=' . $row->id . '>
                                    <i class="bi bi-credit-card-2-back me-2"></i> Receive Payment
                                </a>
                     </li>';

                    $btn .= '<li>
                        <a class="dropdown-item openHistory" href="javascript:void(0)" data-id=' . $row->id . '>
                            <i class="bi bi-list-check me-2"></i> Received History
                        </a>
                     </li>';

                    $btn .= '<li>
                        <button class="dropdown-item sale-delete" data-id="' . $row->id . '">
                            <i class="bi bi-trash me-2"></i> Delete
                        </button>
                     </li>';

                    $btn .= '</ul></div>';

                    return $btn;
                })

                ->rawColumns(['reference_no', 'action'])
                ->make(true);
        }
        return view('backend.sale.index');
    }

    public function create()
    {
        $data = $this->saleservice->parms(auth()->id());
        return view('backend.sale.create', $data);
    }

    public function store(Request $request)
    {
        try {

            $validator = Validator::make(
                $request->all(),
                [
                    'customer_id' => 'required',
                    'sale_date'   => 'required|date',
                    'invoice_no'  => 'required',

                    'product_id'  => 'required|array',
                    'unit_price'  => 'required|array',
                    'quantity'    => 'required|array',

                    'product_id.*' => 'required',
                    'unit_price.*' => 'required|numeric',
                    'quantity.*'   => 'required|numeric',

                    'paid_amount'  => 'required',
                ],
                [
                    'customer_id.required' => 'Please select customer.',
                    'invoice_no.required'  => 'Please enter invoice number.',
                    'quantity.*.required'  => 'Please enter quantity.',
                ]
            );


            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()
                ], 422);
            }


            $userId = auth()->id();

            $data = [
                'user_id'       => $userId,
                'customer_id'   => $request->customer_id,
                'sale_date'     => $request->sale_date,
                'invoice_no'    => $request->invoice_no,
                'reference_no'  => $request->reference_no,
                'subtotal'      => $request->subtotal,
                'tax_amount'    => $request->tax_amount,
                'shipping_charges' => $request->shipping_charges,
                'rounding'      => $request->rounding,
                'discount'      => $request->discount,
                'discount_type' => $request->discount_type,
                'total_amount'  => $request->total_amount,
                'paid_amount'   => $request->paid_amount,
                'due_amount'    => $request->due_amount,
                'payment_method' => $request->payment_method,
                'notes'         => $request->notes,

                // Items
                'product_id'    => $request->product_id,
                'unit_price'    => $request->unit_price,
                'quantity'      => $request->quantity,
                'gst_percent'   => $request->gst_percent,
                'gst_amount'    => $request->gst_amount,
                'total'         => $request->total,
            ];

            $sale = $this->saleservice->saleSaved($data);

            return response()->json([
                'success'  => true,
                'message'  => 'Sale saved successfully!',
                'redirect' => route('sale.show', $sale->id)
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function show(Request $request, $id)
    {
        $data = $this->saleservice->saleDetails($id);
        return view('backend.sale.show', $data);
    }

    public function edit(Request $request, $id)
    {
        $saleData = $this->saleservice->saleEdit($id);
        $params   = $this->saleservice->parms(auth()->id());

        $data = array_merge($saleData, $params);

        return view('backend.sale.edit', $data);
    }


    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'customer_id' => 'required',
                    'sale_date'   => 'required|date',
                    'invoice_no'  => 'required',

                    'product_id'  => 'required|array',
                    'unit_price'  => 'required|array',
                    'quantity'    => 'required|array',

                    'product_id.*' => 'required',
                    'unit_price.*' => 'required|numeric',
                    'quantity.*'   => 'required|numeric',

                    'paid_amount'  => 'required',
                ],
                [
                    'customer_id.required' => 'Please select customer.',
                    'invoice_no.required'  => 'Please enter invoice number.',
                    'quantity.*.required'  => 'Please enter quantity.',
                ]
            );


            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()
                ], 422);
            }


            $userId = auth()->id();

            $data = [
                'user_id'       => $userId,
                'customer_id'   => $request->customer_id,
                'sale_date'     => $request->sale_date,
                'invoice_no'    => $request->invoice_no,
                'subtotal'      => $request->subtotal,
                'tax_amount'    => $request->tax_amount,
                'shipping_charges' => $request->shipping_charges,
                'rounding'      => $request->rounding,
                'discount'      => $request->discount,
                'discount_type' => $request->discount_type,
                'total_amount'  => $request->total_amount,
                'paid_amount'   => $request->paid_amount,
                'due_amount'    => $request->due_amount,
                'payment_method' => $request->payment_method,
                'notes'         => $request->notes,

                // Items
                'product_id'    => $request->product_id,
                'unit_price'    => $request->unit_price,
                'quantity'      => $request->quantity,
                'gst_percent'   => $request->gst_percent,
                'gst_amount'    => $request->gst_amount,
                'total'         => $request->total,
            ];

            $sale = $this->saleservice->updateSale($data, $id);

            return response()->json([
                'success'  => true,
                'message'  => 'Sale updated successfully!',
                'redirect' => route('sale.show', $sale->id)
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'sale_id' => 'required|exists:sales,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()
                ], 422);
            }

            $deleted = $this->saleservice->deleteSale($validator->validate());

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to delete sale.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sale deleted successfully.'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function GetSaleData(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:sales,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()
                ], 422);
            }

            $data = $this->saleservice->receive_payment_data($validator->validate());

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to data sale.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function ReceivePayment(Request $request, $id)
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

            $paymentSuccess = $this->saleservice->receiveAmount($data, $id);

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

    public function ReceiveHistory(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:sales,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()
                ], 422);
            }

            $data = $this->saleservice->saleReceiveHistory($validator->validate());

            return response()->json([
                'success'  => true,
                'data'  => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteReceivePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|exists:party_transactions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        try {

            $this->saleservice->deletePayment($validator->validate());

            return response()->json([
                'success' => true,
                'message' => "Sale payment entry deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function print($id)
    {
        $data = $this->saleservice->invoicePrint($id);
        return view('backend.sale.print', $data);
    }

    public function generatePdf($id)
    {
        $sale = Sales::with(['customer', 'items.product'])->findOrFail($id);
        $company = CompanyDetails::where('user_id', auth()->id())->first();

        // Clean filename
        $cleanRef = preg_replace('/[\/\\\\]/', '-', $sale->reference_no);
        $fileName = 'Invoice_' . $cleanRef . '.pdf';

        // Convert logo to base64 for PDF
        $logo = null;
        if ($company->company_logo && file_exists(public_path($company->company_logo))) {
            $logo = base64_encode(file_get_contents(public_path($company->company_logo)));
        }

        // Pass logo to view
        $pdf = Pdf::loadView('backend.sale.saleinvoice', [
            'sale' => $sale,
            'company' => $company,
            'logo' => $logo
        ])
            ->setPaper('A4', 'portrait')
            ->set_option('isHtml5ParserEnabled', true)
            ->set_option('isRemoteEnabled', true);

        return $pdf->download($fileName);
    }
}
