<?php

namespace App\Services;

use App\Models\ExpenseAttachment;
use App\Models\ExpenseCategory;
use App\Models\ExpenseItems;
use App\Models\ExpenseLedger;
use App\Models\ExpensePayment;
use App\Models\Expenses;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ExpenseService
{

    public function getAllExpensCategory($userId)
    {
        return ExpenseCategory::where('user_id', $userId)
            ->where('status', 1)
            ->get();
    }

    public function getAll($userId)
    {
        return Expenses::with('category')
            ->where('user_id', $userId)
            ->latest();
    }


    public function findById($id)
    {
        return Expenses::with(['payments', 'items'])->find($id);
    }


    public function createExpense(array $data)
    {
        DB::transaction(function () use ($data) {
            $total = $data['total_amount'];
            $paid  = $data['paid_amount'] ?? 0;
            $due   = $total - $paid;


            $expenseDate = Carbon::createFromFormat('d-m-Y', $data['expense_date'])->format('Y-m-d');

            // EXPENSE MASTER
            $expense = Expenses::create([
                'expense_date'   => $expenseDate,
                'category_id'    => $data['category_id'],
                'paid_to'        => $data['paid_to'] ?? null,
                'total_amount'   => $total,
                'paid_amount'    => $paid,
                'due_amount'     => $due,
                'payment_status' => $paid == $total ? 'paid' : ($paid > 0 ? 'partial' : 'due'),
                'notes'          => $data['notes'] ?? null,
                'user_id'        => auth()->id(),
            ]);

            //ITEMS SAVE

            foreach ($data['items'] as $item) {

                if (empty($item['amount'])) continue;

                $amount = $item['amount'] ?? 0;
                $tax    = $item['tax_amount'] ?? 0;

                $total  = $amount + $tax;

                ExpenseItems::create([
                    'expense_id'  => $expense->id,
                    'category_id' => $expense->category_id,
                    'description' => $item['description'] ?? null,
                    'amount'      => $amount,
                    'tax_amount'  => $tax,
                    'total'       => $total,
                ]);
            }


            // LEDGER ENTRY (DEBIT)
            ExpenseLedger::create([
                'expense_id' => $expense->id,
                'user_id'    => auth()->id(),
                'type'       => 'debit',
                'amount'     => $total,
                'note'       => 'Expense Created',
            ]);

            // PAYMENT ENTRY
            if ($paid > 0) {

                ExpensePayment::create([
                    'expense_id'     => $expense->id,
                    'payment_date'   => $expenseDate,
                    'amount'         => $paid,
                    'payment_method' => $data['payment_method'],
                    'reference_no'   => $data['reference_no'],
                    'note'           => 'Expense Payment',
                ]);

                // LEDGER PAYMENT
                ExpenseLedger::create([
                    'expense_id' => $expense->id,
                    'user_id'    => auth()->id(),
                    'type'       => 'payment',
                    'amount'     => $paid,
                    'note'       => 'Expense Payment',
                ]);
            }

            //ATTACHMENT (OPTIONAL)
            if (!empty($data['attachment'])) {
                ExpenseAttachment::create([
                    'expense_id' => $expense->id,
                    'file_path'  => $data['attachment'],
                ]);
            }
        });

        return true;
    }

    public function expenseUpdate(array $data, $id)
    {
        DB::transaction(function () use ($data, $id) {

            $expense = Expenses::findOrFail($id);

            $total = $data['total_amount'];
            $paid  = $data['paid_amount'] ?? 0;
            $due   = $total - $paid;

            $expenseDate = Carbon::createFromFormat('d-m-Y', $data['expense_date'])->format('Y-m-d');

            $expense->update([
                'expense_date'   => $expenseDate,
                'category_id'    => $data['category_id'],
                'paid_to'        => $data['paid_to'] ?? null,
                'total_amount'   => $total,
                'paid_amount'    => $paid,
                'due_amount'     => $due,
                'payment_status' => $paid == $total ? 'paid' : ($paid > 0 ? 'partial' : 'due'),
                'notes'          => $data['notes'] ?? null,
            ]);

            $expense->items()->delete();
            $expense->payments()->delete();
            ExpenseLedger::where('expense_id', $id)->delete();

            foreach ($data['items'] as $item) {

                if (empty($item['amount'])) continue;

                $amount = $item['amount'] ?? 0;
                $tax    = $item['tax_amount'] ?? 0;

                $total  = $amount + $tax;

                ExpenseItems::create([
                    'expense_id'  => $expense->id,
                    'category_id' => $expense->category_id,
                    'description' => $item['description'] ?? null,
                    'amount'      => $amount,
                    'tax_amount'  => $tax,
                    'total'       => $total,
                ]);
            }


            // LEDGER ENTRY (DEBIT)
            ExpenseLedger::create([
                'expense_id' => $expense->id,
                'user_id'    => auth()->id(),
                'type'       => 'debit',
                'amount'     => $total,
                'note'       => 'Expense Created',
            ]);

            // PAYMENT ENTRY
            if ($paid > 0) {

                ExpensePayment::create([
                    'expense_id'     => $expense->id,
                    'payment_date'   => $expenseDate,
                    'amount'         => $paid,
                    'payment_method' => $data['payment_method'],
                    'reference_no'   => $data['reference_no'],
                    'note'           => 'Expense Payment',
                ]);

                // LEDGER PAYMENT
                ExpenseLedger::create([
                    'expense_id' => $expense->id,
                    'user_id'    => auth()->id(),
                    'type'       => 'payment',
                    'amount'     => $paid,
                    'note'       => 'Expense Payment',
                ]);
            }

            //ATTACHMENT (OPTIONAL)
            if (!empty($data['attachment'])) {
                ExpenseAttachment::create([
                    'expense_id' => $expense->id,
                    'file_path'  => $data['attachment'],
                ]);
            }
        });

        return true;
    }
}
