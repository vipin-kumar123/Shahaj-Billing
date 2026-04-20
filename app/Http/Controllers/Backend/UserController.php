<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{

    protected $userservice;

    public function __construct(UserService $userservice)
    {
        $this->userservice = $userservice;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = $this->userservice->userList();

            return DataTables::of($data)

                // STATUS COLUMN
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


                // ROLE COLUMN
                ->addColumn('role', function ($row) {
                    return $row->roles->pluck('name')->implode(', '); // user->roles from Spatie
                })

                // ACTION COLUMN
                ->addColumn('action', function ($row) {
                    $btn  = '<button class="btn btn-danger btn-sm me-2 delete" data-id="' . $row->id . '">
                        <i class="bi bi-trash"></i>
                    </button>';
                    $btn .= '<a href="' . route('users.edit', $row->id) . '" class="btn btn-success btn-sm me-2">
                        <i class="bi bi-pencil-square"></i>
                    </a>';
                    $btn .= '<a href="' . route('users.show', $row->id) . '" class="btn btn-primary btn-sm view" data-id="' . $row->id . '">
                        <i class="bi bi-eye"></i>
                    </a>';

                    return $btn;
                })

                ->rawColumns(['is_active', 'role', 'action'])
                ->make(true);
        }

        return view('backend.users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = $this->userservice->getrole();
        return view('backend.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
            'role_id' => 'required|exists:roles,id',
        ]);

        $this->userservice->storeUser($validatedData);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = $this->userservice->showUser($id);

        return view('backend.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $roles = $this->userservice->getrole();
        $user = $this->userservice->editUser($id);

        // $userRole = $user->roles->pluck('name', 'name')->all();
        $userRoleId = $user->roles->first()?->id;
        return view('backend.users.edit', compact('roles', 'user', 'userRoleId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:6|confirmed',
            'password_confirmation' => 'nullable',
            'role_id' => 'required|exists:roles,id',
        ]);

        $this->userservice->updateUser($validatedData, $id);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function updateStatus(Request $request, $id)
    {
        $this->userservice->userIsactive($request, $id);

        return response()->json(['success' => true]);
    }
}
