<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{

    protected $suppliserService;

    public function __construct(SupplierService $suppliserService)
    {
        $this->suppliserService = $suppliserService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->suppliserService->supplierList();
            return DataTables::of($data)

                ->editColumn('name', function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                })

                ->addColumn('balance_type', function ($row) {
                    return 'No Balance';
                })

                ->addColumn('created_by', function ($row) {
                    return $row->user?->name ?? '';
                })

                ->editColumn('created_at', function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y');
                })

                // ->filterColumn('brand', function ($query, $keyword) {
                //     $query->whereHas('brand', function ($q) use ($keyword) {
                //         $q->where('name', 'like', "%{$keyword}%");
                //     });
                // })

                ->editColumn('is_active', function ($row) {

                    $checked = $row->is_active ? 'checked' : '';

                    return '
                            <div class="form-check form-switch">
                                <input class="form-check-input supplier-status-toggle" 
                                    type="checkbox" 
                                    data-id="' . $row->id . '" 
                                    ' . $checked . '>
                            </div>
                        ';
                })

                ->addColumn('action', function ($row) {
                    $btn = '<div class="dropdown position-static">
                            <button class="p-0 px-1" data-bs-toggle="dropdown" data-bs-flip="true" data-bs-boundary="viewport" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical fs-5"></i>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end shadow">';

                    $btn .= '<li>
                                <a class="dropdown-item" href="' . route('supplier.edit', $row->id) . '">
                                    <i class="bi bi-pencil-square me-2"></i> Edit
                                </a>
                            </li>';


                    $btn .= '<li>
                                <a class="dropdown-item" href="">
                                    <i class="bi bi-credit-card me-2"></i> Payment
                                </a>
                             </li>';

                    $btn .= '<li>
                                <a class="dropdown-item" href="#">
                                    <i class="bi bi-list me-2"></i> Payment History
                                </a>
                            </li>';

                    $btn .= '<li>
                                <button class="dropdown-item supplier-delete" data-id="' . $row->id . '">
                                    <i class="bi bi-trash me-2"></i> Delete
                                </button>
                            </li>';

                    $btn .= '</ul>
                            </div>';

                    return $btn;
                })

                ->rawColumns(['action', 'is_active'])
                ->make(true);
        }
        return view('backend.supplier.index');
    }

    public function create()
    {
        $data = $this->suppliserService->commanData();
        return view('backend.supplier.create', $data);
    }


    public function store(Request $request)
    {
        $validator = $request->validate([
            'first_name' => 'required',
            'last_name'  => 'nullable',
            'email' => 'nullable|email|unique:customers,email',
            'mobile_number' => 'required|digits:10|unique:customers,mobile_number',
            'alternate_number' => 'nullable|digits:10',
            'whatsapp_number' => 'nullable|digits:10',
            'village' => 'nullable',
            'mohalla' => 'nullable',
            'district' => 'nullable',
            'area' => 'nullable',
            'city' => 'nullable',
            'state' => 'nullable',
            'pincode' => 'nullable|digits:6',
            'billing_address' => 'nullable',
            'shipping_address' => 'nullable',
            'is_business' => 'nullable|boolean',
            'business_name' => 'nullable',
            'gst_number' => 'nullable|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
            'pan_number' => 'nullable|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
            'opening_balance' => 'nullable|numeric|min:0',
            'udhar_limit' => 'nullable|numeric|min:0',
        ]);

        $this->suppliserService->supplierStore($validator);

        return redirect()->route('supplier.index')->with('success', 'Supplier created successfully!');
    }


    public function isActive(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'     => 'required|exists:customers,id',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        $status = $this->suppliserService->statusUpdate($validator->validate());

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Failed request'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.'
        ]);
    }


    public function edit($id)
    {
        $supplier = $this->suppliserService->editSupplier($id);
        $data = $this->suppliserService->commanData();
        return view('backend.supplier.edit', compact('supplier'), $data);
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'first_name' => 'required',
            'last_name'  => 'nullable',
            'email' => 'nullable|email|unique:customers,email,' . $id,
            'mobile_number' => 'required|digits:10|unique:customers,mobile_number,' . $id,
            'alternate_number' => 'nullable|digits:10',
            'whatsapp_number' => 'nullable|digits:10',
            'village' => 'nullable',
            'mohalla' => 'nullable',
            'district' => 'nullable',
            'area' => 'nullable',
            'city' => 'nullable',
            'state' => 'nullable',
            'pincode' => 'nullable|digits:6',
            'billing_address' => 'nullable',
            'shipping_address' => 'nullable',
            'is_business' => 'nullable|boolean',
            'business_name' => 'nullable',
            'gst_number' => 'nullable|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
            'pan_number' => 'nullable|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
            'opening_balance' => 'nullable|numeric|min:0',
            'udhar_limit' => 'nullable|numeric|min:0',
        ]);

        $this->suppliserService->supplierUpdate($validator, $id);

        return redirect()->route('supplier.index')->with('success', 'Supplier updated successfully!');
    }

    public function stateGetCity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'state_id'     => 'required|exists:states,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        $data = $this->suppliserService->getCity($validator->validate());

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'No cities found for this state.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
