<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\SubcategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SubcategoryController extends Controller
{
    protected $subcatservice;

    public function __construct(SubcategoryService $subcatservice)
    {
        $this->subcatservice = $subcatservice;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->subcatservice->subcategoryList();

            return DataTables::of($data)

                ->editColumn('is_active', function ($row) {
                    $checked = $row->is_active ? 'checked' : '';
                    return '<div class="form-check form-switch">
                        <input class="form-check-input sub-status-toggle" 
                            type="checkbox" 
                            data-id="' . $row->id . '" ' . $checked . '>
                    </div>';
                })

                ->addColumn('category', function ($row) {
                    return $row->category?->name ?? '-';
                })

                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-danger btn-sm me-1 delete-subcat" data-id="' . $row->id . '">
                              <i class="bi bi-trash"></i>
                            </button>';

                    $btn .= '<button type="button" class="btn btn-success btn-sm me-1 edit-subcat" data-id="' . $row->id . '">
                        <i class="bi bi-pencil-square"></i></button>';

                    $btn .= '<a href="javascript:void(0)" class="btn btn-primary btn-sm show-subcat" data-id="' . $row->id . '">
                        <i class="bi bi-eye"></i></a>';

                    return $btn;
                })

                ->rawColumns(['is_active', 'action'])
                ->make(true);
        }

        $categories = $this->subcatservice->categoryList();
        return view('backend.subcategory.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'        => 'required|integer|not_in:0',
            'name'               => 'required|array|min:1',
            'name.*'             => 'required|string|max:255',
            'slug'               => 'required|array',
            'slug.*'             => 'required|string|max:255',
            'description'        => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $this->subcatservice->subStore($validator->validate());

        return response()->json([
            'success' => true,
            'message' => 'Sub category created successfully'
        ], 200);
    }


    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:sub_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $this->subcatservice->subcatEdit($validator->validated());

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }


    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subcatid'      => 'required|exists:sub_categories,id',
            'category_id'   => 'required|integer|not_in:0',
            'name'          => 'required|unique:sub_categories,name,' . $request->subcatid,
            'slug'          => 'required|unique:sub_categories,slug,' . $request->subcatid,
            'description'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $update = $this->subcatservice->subcatUpdate($validator->validate());
        if (!$update) {
            return response()->json([
                'success' => false,
                'message' => 'Sub category not update successfully'
            ], 403);
        }
        return response()->json([
            'success' => true,
            'message' => 'Sub category updated successfully'
        ]);
    }


    public function statusUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'     => 'required|exists:sub_categories,id',
            'status' => 'required|in:0,1', // validate allowed values
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $updated = $this->subcatservice->subStatusUpdate($validator->validated());

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
        ]);
    }


    public function deleteSub(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'     => 'required|exists:sub_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $this->subcatservice->destroy($validator->validate());

        return response()->json([
            'success' => true,
            'message' => 'Sub category deleted successfully!',
        ]);
    }

    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'     => 'required|exists:sub_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $this->subcatservice->display($validator->validate());

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
