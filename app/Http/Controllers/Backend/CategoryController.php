<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Nette\Utils\Json;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{

    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->categoryService->categoryList();

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
                    return '
                            <button class="btn btn-danger btn-sm me-1 cat-delete" data-id="' . $row->id . '">
                                <i class="bi bi-trash"></i>
                            </button>
                            <a href="javascript:void(0)" class="btn btn-success btn-sm me-1 edit-category" data-id="' . $row->id . '"><i class="bi bi-pencil-square"></i></a>
                            <a href="javascript:void(0)" class="btn btn-primary btn-sm show-category" data-id="' . $row->id . '"><i class="bi bi-eye"></i></a>
                        ';
                })
                ->rawColumns(['action', 'is_active'])
                ->make(true);
        }
        return view('backend.category.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|unique:categories,name',
            'slug'        => 'required',
            'description' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $this->categoryService->storeCategory($validator->validate());

        return response()->json([
            'success' => true,
            'message'    => 'Category has been created successfully'
        ]);
    }


    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'        => 'required|exists:categories,id',
            'status'    => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $this->categoryService->isActive($validator->validate());

        return response()->json([
            'success' => true,
            'message'    => 'Status change successfully'
        ]);
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'        => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $this->categoryService->categoryEdit($validator->validate());

        return response()->json([
            'success' => true,
            'data'  => $data
        ], 200);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'catid' => 'required|exists:categories,id',

            'name' => [
                'required',
                Rule::unique('categories', 'name')->ignore($request->catid)
            ],

            'slug' => [
                'required',
                Rule::unique('categories', 'slug')->ignore($request->catid)
            ],

            'description' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $this->categoryService->categoryUpdate($validator->validate());

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully'
        ]);
    }


    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $this->categoryService->categoryEdit($validator->validate());

        return response()->json([
            'success' => true,
            'data'  => $data
        ], 200);
    }


    public function delete(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:categories,id',
        ]);

        $deleted = $this->categoryService->categoryDelete($data);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ]);
    }
}
