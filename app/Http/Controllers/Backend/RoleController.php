<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{

    protected $roleservice;

    public function __construct(RoleService $roleservice)
    {
        $this->roleservice = $roleservice;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->roleservice->GetRoles();
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-danger btn-sm me-2" data-id="' . $row->id . '"><i class="bi bi-trash"></i></button>';
                    $btn .= '<a href="' . route('roles.edit', $row->id) . '" class="btn btn-success btn-sm me-2"><i class="bi bi-pencil-square"></i></a>';
                    $btn .= '<a href="' . route('roles.show', $row->id) . '" class="btn btn-primary btn-sm"><i class="bi bi-eye"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.roles.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = $this->roleservice->getPermission();
        return view('backend.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // VALIDATION (returns validated DATA array)
        $validatedData = $request->validate([
            'name' => 'required|unique:roles,name',
            'permission_id' => 'required|array|min:1',
        ]);

        // CALL SERVICE
        $this->roleservice->storeRole($validatedData);

        return redirect()->back()->with('success', 'Role created successfully!');
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = $this->roleservice->showRole($id);
        return view('backend.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $permissions = $this->roleservice->getPermission();
        $role = $this->roleservice->editRole($id);
        // role ke existing permissions (names)
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('backend.roles.edit', compact(
            'role',
            'permissions',
            'rolePermissions'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permission_id' => 'required|array|min:1',
        ]);

        $this->roleservice->roleUpdate($validatedData, $role);

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully!');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
