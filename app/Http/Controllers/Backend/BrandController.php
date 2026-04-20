<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\BrandService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    protected $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    // return response()->json([
    //     'data' => $request->all(),

    // ]);

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = $this->brandService->brandList(auth()->id());

            return DataTables::of($data)

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
                    $btn = '<button class="btn btn-danger btn-sm me-1 brand-delete" data-id="' . $row->id . '">
                            <i class="bi bi-trash"></i>
                        </button>';

                    $btn .= '<a href="javascript:void(0)" class="btn btn-success btn-sm me-1 edit-brand" data-id="' . $row->id . '">
                            <i class="bi bi-pencil-square"></i>
                        </a>';

                    $btn .= '<a href="javascript:void(0)" class="btn btn-primary btn-sm show-brand" data-id="' . $row->id . '">
                            <i class="bi bi-eye"></i>
                        </a>';

                    return $btn;
                })

                ->rawColumns(['action', 'is_active'])
                ->make(true);
        }

        return view('backend.brands.index');
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|unique:brands,name',
            'description' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->brandService->saveBrand($validator->validate());

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Failed request'
            ], 500);
        }
        // success
        return response()->json([
            'success' => true,
            'message' => 'Brand created successfully'
        ], 200);
    }


    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'        => 'required|exists:brands,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $this->brandService->destroy($validator->validate());

        return response()->json([
            'success' => true,
            'message' => 'Brand has been deleted successfully'
        ], 200);
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'        => 'required|exists:brands,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->brandService->editBrand($validator->validate());

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand_id'    => 'required|exists:brands,id',
            'name'        => 'required|unique:brands,name,' . $request->brand_id,
            'description' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $this->brandService->updateBrand($validator->validate());

        return response()->json([
            'success' => true,
            'message' => 'Brand has been updated successfully'
        ], 200);
    }


    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'        => 'required|exists:brands,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->brandService->showBrand($validator->validate());

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
