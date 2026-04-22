@extends('backend.layouts.app')
@section('title')
    Billing Software | Expenses
@endsection


@section('content')
    <div class="container-fluid px-2">

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">

            <!-- LEFT -->
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Expenses</span>
                <i class="bi bi-chevron-right mx-2"></i>

                <span class="fw-semibold text-dark">Create Expenses</span>
            </div>

            <!-- RIGHT -->
            <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="{{ route('expenses.index') }}" class="mdc-button mdc-button--danger btn-sm">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

        </div>



        <!-- CARD -->
        <div class="card border-0 shadow-sm">
            <div class="card-body px-5 py-4">

                <form id="expenseForm">
                    @csrf
                    <!-- Row 1 -->
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="mb-1">Date*</label>
                            <input type="text" name="expense_date" class="form-control datepicker"
                                value="{{ date('d-m-Y') }}" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="mb-1">Category</label>
                            <select name="category_id" class="form-control select2">
                                <option value="">Select Category</option>
                                @foreach ($excats ?? [] as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="mb-1">Paid To</label>
                            <input type="text" name="paid_to" class="form-control" placeholder="Vendor / Person">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="mb-1">Payment Method</label>
                            <select name="payment_method" class="form-control select2">
                                <option value="cash">Cash</option>
                                <option value="bank">Bank</option>
                                <option value="upi">UPI</option>
                            </select>
                        </div>

                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th width="150">Amount</th>
                                    <th width="150">Tax</th>
                                    <th width="80">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="mdc-button mdc-button--primary btn-sm mb-3" id="addRow">
                        + Add Row
                    </button>

                    <!-- Amount Section -->
                    <div class="row">

                        <div class="col-md-3 mb-3">
                            <label>Total Amount</label>
                            <input type="number" id="total_amount" name="total_amount" class="form-control" readonly>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Paid Amount</label>
                            <input type="number" name="paid_amount" id="paid_amount" class="form-control">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Due Amount</label>
                            <input type="number" name="due_amount" id="due_amount" class="form-control" readonly>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Payment Reference No</label>
                            <input type="text" name="reference_no" class="form-control">
                        </div>

                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>

                    <!-- Submit -->
                    <div class="text-end">
                        <button id="expenseBtn" class="mdc-button mdc-button--success">
                            Save Expense
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
@endsection


@push('footer-script')
    <script>
        window.EXPENSE_INDEX = "{{ route('expenses.index') }}";
        window.EXPENSE_STORE = "{{ route('expenses.store') }}";
    </script>

    <script src="{{ asset('assets/backend/js/expense.js') }}"></script>
@endpush
