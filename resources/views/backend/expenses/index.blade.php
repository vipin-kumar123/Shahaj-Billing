@extends('backend.layouts.app')
@section('title')
    Billing Software | Expenses
@endsection


@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Expenses</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>List</span>
                {{-- <i class="bi bi-chevron-right mx-2"></i>
                <span class="fw-semibold text-dark">Create Purchase</span> --}}
            </div>

            <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="{{ route('expenses.create') }}" class="mdc-button mdc-button--unelevated">
                    <i class="bi bi-plus"></i> Add Expenses
                </a>
            </div>
        </div>


        <!-- CARD -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-2">

                <table id="expenseTable" class="table table-hover mb-0" style="width:100%; table-layout: fixed;">
                    <thead class="bg-light">
                        <tr>
                            <th>Expense Date</th>
                            <th>Expense No.</th>
                            <th>Category</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Due Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                    </tbody>

                </table>
            </div>
        </div>
    </div>
@endsection


@push('footer-script')
    <script>
        window.EXPENSE_LIST_ROUTE = "{{ route('expenses.list') }}";
    </script>

    <script src="{{ asset('assets/backend/js/expense.js') }}"></script>
@endpush
