<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\ExpenseService;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ExpensesController extends Controller
{
    protected $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

    public function index()
    {
        return view('backend.expenses.index');
    }


    public function list(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->expenseService->getAll(auth()->id());

            return DataTables::of($data)
                ->editColumn('payment_status', function ($row) {
                    $status = strtolower($row->payment_status);

                    $classes = [
                        'paid' => 'success',
                        'partial' => 'warning text-dark',
                        'due' => 'danger',
                    ];

                    $labels = [
                        'paid' => 'Paid',
                        'partial' => 'Partial',
                        'due' => 'Due',
                    ];

                    $class = $classes[$status] ?? 'secondary';
                    $label = $labels[$status] ?? 'Unknown';

                    return "<span class='badge rounded-pill bg-{$class}'>{$label}</span>";
                })
                ->editColumn('expense_date', function ($row) {
                    return $row->expense_date
                        ? \Carbon\Carbon::parse($row->expense_date)->format('d-m-Y')
                        : '';
                })
                ->editColumn('category', function ($row) {
                    return $row->category?->name ?? '';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="dropdown position-static">
                    <button class="p-0 px-1" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical fs-5"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-pencil-square me-2"></i> Edit
                            </a>
                        </li>
                    </ul>
                </div>';
                })
                ->rawColumns(['action', 'payment_status'])
                ->make(true);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }


    public function create()
    {
        $excats = $this->expenseService->getAllExpensCategory(auth()->id());
        return view('backend.expenses.create', compact('excats'));
    }

    public function store(Request $request)
    {

        // return response()->json([
        //     'data' =>  $request->all(),
        // ]);
        // exit;
        $validator = Validator::make($request->all(), [
            'expense_date'   => 'required|date',
            'payment_method' => 'required|string|max:50',
            'category_id'    => 'required|exists:expense_categories,id',
            'paid_to'        => 'nullable|string|max:255',
            'paid_amount'    => 'nullable|numeric|min:0',
            'total_amount'   => 'required|numeric|min:0',
            'reference_no'   => 'nullable',

            'items' => 'required|array|min:1',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.description' => 'nullable|string',

            'notes' => 'nullable|string',
        ]);

        $this->expenseService->createExpense($validator->validate());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Expense added successfully'
        ]);
    }
}
