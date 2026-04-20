<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{

    protected $productservice;

    public function __construct(ProductService $productservice)
    {
        $this->productservice = $productservice;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->productservice->product_list();
            return DataTables::of($data)

                ->editColumn('brand', function ($row) {
                    return $row->brand?->name ?? '-';
                })

                ->filterColumn('brand', function ($query, $keyword) {
                    $query->whereHas('brand', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })

                ->editColumn('is_active', function ($row) {

                    $checked = $row->is_active ? 'checked' : '';

                    return '
                            <div class="form-check form-switch">
                                <input class="form-check-input user-status-toggle" 
                                    type="checkbox" 
                                    data-id="' . $row->id . '" 
                                    ' . $checked . '>
                            </div>
                        ';
                })

                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-danger btn-sm me-1 item-delete" data-id="' . $row->id . '">
                                <i class="bi bi-trash"></i>
                            </button>';
                    $btn .= '<a href="' . route('products.edit', $row->id) . '" class="btn btn-success btn-sm me-1"><i class="bi bi-pencil-square"></i></a>';
                    $btn .= '<a href="' . route('products.show', $row->id) . '" class="btn btn-primary btn-sm"><i class="bi bi-eye"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'is_active'])
                ->make(true);
        }
        return view('backend.products.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $this->productservice->master_data();
        return view('backend.products.create', $data);
    }


    public function getSubcategories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->productservice->GetCategory($validator->validate());

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'category_id'        => 'required|exists:categories,id',
            'sub_category_id'    => 'nullable|exists:sub_categories,id',
            'brand_id'           => 'nullable|exists:brands,id',

            'name'               => 'required|string|max:255|unique:products,name',
            'slug'               => 'nullable|string|max:255|unique:products,slug',

            'barcode_code'       => 'nullable|string|max:50|unique:products,barcode_code',
            'hsn_code'           => 'nullable|string|max:20',
            'sku'                => 'nullable|string|max:20',

            'unit'               => 'nullable|string|max:20',

            'purchase_price'     => 'nullable|numeric|min:0',
            'distributor_price'  => 'nullable|numeric|min:0',
            'wholesale_price'    => 'nullable|numeric|min:0',
            'saleing_price'      => 'nullable|numeric|min:0',

            'gst_percentage'     => 'nullable|numeric|min:0|max:28',

            'opening_stock'      => 'nullable|integer|min:0',
            'low_stock_alert'    => 'nullable|integer|min:0',

            'product_type'       => 'required|in:simple,variant,service',

            'status'             => 'nullable|in:0,1',
        ]);


        $this->productservice->saved_product($validated);

        return redirect()->route('products.index')->with('success', 'Product added successfully!');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = $this->productservice->show_product($id);
        return view('backend.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = $this->productservice->edit_product($id);
        $data = $this->productservice->master_data();
        return view('backend.products.edit', compact('item'), $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([

            'category_id'        => 'required|exists:categories,id',
            'sub_category_id'    => 'nullable|exists:sub_categories,id',
            'brand_id'           => 'nullable|exists:brands,id',

            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')->ignore($id),
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($id),
            ],

            'barcode_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('products', 'barcode_code')->ignore($id),
            ],

            'hsn_code'           => 'nullable|string|max:20',
            'sku'                => 'nullable|string|max:20',
            'unit'               => 'nullable|string|max:20',

            'purchase_price'     => 'nullable|numeric|min:0',
            'distributor_price'  => 'nullable|numeric|min:0',
            'wholesale_price'    => 'nullable|numeric|min:0',
            'saleing_price'      => 'nullable|numeric|min:0',

            'gst_percentage'     => 'nullable|numeric|min:0|max:28',

            'opening_stock'      => 'nullable|integer|min:0',
            'low_stock_alert'    => 'nullable|integer|min:0',

            'product_type'       => 'required|in:simple,variant,service',
            'status'             => 'nullable|in:0,1',
        ]);

        $this->productservice->update_product($validated, $id);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function trash(Request $request)
    {
        $delete = $this->productservice->destroy($request->id);

        if (!$delete) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found or delete failed'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item has been deleted successfully'
        ], 200);
    }
}
