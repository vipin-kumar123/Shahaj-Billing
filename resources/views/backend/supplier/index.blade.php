@extends('backend.layouts.app')
@section('title')
    Billing Software | Supplier
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
                <span>Supplier List</span>
            </div>

            <!-- RIGHT -->
            <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="#" class="mdc-button mdc-button--outlined">Import</a>
                <a href="{{ route('supplier.create') }}" class="mdc-button mdc-button--unelevated">
                    + Create Supplier
                </a>
            </div>

        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm">

            <div class="card-body p-3">

                <h5 class="fw-semibold mb-2">SUPPLIER LIST</h5>
                <hr>

                <!-- RESPONSIVE TABLE -->
                <div class="table-responsive">
                    <table id="supplierTable" class="table table-hover table-sm mb-0">
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
        window.SUPPLIER_INDEX_ROUTE = "{{ route('supplier.index') }}";
        window.SUPPLIER_STATUS_ROUTE = "{{ route('supplier.isactive') }}";
    </script>

    <script src="{{ asset('assets/backend/js/supplier.js') }}"></script>
@endpush
