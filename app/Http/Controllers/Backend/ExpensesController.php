<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\ExpenseService;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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


    public function create()
    {
        $excats = $this->expenseService->getAllExpensCategory(auth()->id());
        return view('backend.expenses.create', compact('excats'));
    }

    public function store(Request $request)
    {
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
