@extends('backend.layouts.app')
@section('title')
    Billing Software | Customers
@endsection

@section('content')
    <div class="container-fluid px-2">

        <!-- BREADCRUMB -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">

            <!-- LEFT -->
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Contacts</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>Customer List</span>
            </div>

            <!-- RIGHT -->
            <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="#" class="mdc-button mdc-button--outlined">Import</a>
                <a href="{{ route('customers.create') }}" class="mdc-button mdc-button--unelevated">
                    + Create Customer
                </a>
            </div>

        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm">

            <div class="card-body p-3">

                <h5 class="fw-semibold mb-2">CUSTOMER LIST</h5>
                <hr>

                <!-- RESPONSIVE TABLE -->
                <div class="table-responsive">
                    <table id="customerTable" class="table table-hover table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>WhatsApp</th>
                                <th>Email</th>
                                <th>Balance</th>
                                <th>Balance Type</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
@endsection



@push('footer-script')
    <script>
        window.CUSTOMER_INDEX_ROUTE = "{{ route('customers.index') }}";
        window.CUSTOMER_STATUS_ROUTE = "{{ route('customers.status.change') }}";
        window.CUSTOMER_DELETE_ROUTE = "{{ route('customers.delete.record') }}";
    </script>

    <script src="{{ asset('assets/backend/js/customer.js') }}"></script>
@endpush
