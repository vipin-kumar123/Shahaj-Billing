<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $data = Permission::latest();

            return DataTables::of($data)

                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-danger btn-sm delete" data-id="' . $row->id . '"><i class="bi bi-trash"></i></button>';
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.permissions.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:permissions,name'
        ]);

        Permission::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        return response()->json([
            'message' => 'Permission added successfully!'
        ], 200);
    }
}
