<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Services\AccountService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AccountController extends Controller
{
    protected $accountservice;

    public function __construct(AccountService $accountservice)
    {
        $this->accountservice = $accountservice;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = $this->accountservice->accountList(auth()->id());

            return DataTables::of($data)

                ->addIndexColumn()

                ->editColumn('type', function ($row) {
                    return ucfirst($row->type);
                })

                ->editColumn('parent_id', function ($row) {
                    return $row->parent ? $row->parent->name : '-';
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
                    $btn = '<button class="btn btn-danger btn-sm me-1 accountDelete" data-id="' . $row->id . '">
                            <i class="bi bi-trash"></i>
                        </button>';

                    $btn .= '<a href="javascript:void(0)" class="btn btn-success btn-sm me-1 editAccount" data-id="' . $row->id . '">
                            <i class="bi bi-pencil-square"></i>
                        </a>';

                    return $btn;
                })

                ->rawColumns(['action', 'is_active'])
                ->make(true);
        }

        $data = $this->accountservice->params(auth()->id());

        return view('backend.accounts.index', $data);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:accounts,name',
            'type'      => 'required|in:asset,liability,income,expense',
            'parent_id' => 'nullable|exists:accounts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Parent type validation
        if (!empty($validated['parent_id'])) {
            $parent = Account::where('user_id', auth()->id())
                ->find($validated['parent_id']);

            if (!$parent) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'parent_id' => ['Selected parent account is invalid.']
                    ]
                ], 422);
            }

            if ($parent->type !== $validated['type']) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'parent_id' => ['Parent account type must match selected account type.']
                    ]
                ], 422);
            }
        }

        $saved = $this->accountservice->saveAccount($validated, auth()->id());

        if (!$saved) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create account.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully'
        ], 200);
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:accounts,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $this->accountservice->editAccount($validator->validate());

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $account = Account::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255|unique:accounts,name,' . $account->id,
            'type'      => 'required|in:asset,liability,income,expense',
            'parent_id' => 'nullable|exists:accounts,id',
        ]);

        $validator->after(function ($validator) use ($request, $account) {
            if ($request->filled('parent_id')) {
                $parent = Account::find($request->parent_id);

                if ($parent && $parent->type !== $request->type) {
                    $validator->errors()->add('parent_id', 'Parent account type must match selected account type.');
                }

                if ((int) $request->parent_id === (int) $account->id) {
                    $validator->errors()->add('parent_id', 'An account cannot be its own parent.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $this->accountservice->accountUpdate($request->all(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Account updated successfully.',
        ]);
    }

    public function deleteData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'account_id' => 'required|exists:accounts,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $validated = $validator->validate();

            $this->accountservice->deleteAccount($validated);

            return response()->json([
                'success' => true,
                'message' => 'Account deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while deleting account.',
            ], 500);
        }
    }
}
