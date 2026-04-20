@extends('backend.layouts.app')
@section('title')
    Billing Software | Purchase List
@endsection

@section('content')
    <div class="container-fluid px-2">

        <!-- BREADCRUMB -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">

            <!-- LEFT -->
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Purchase</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>Purchase List</span>
            </div>

            <!-- RIGHT -->
            <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="{{ route('purchase.create') }}" class="mdc-button mdc-button--unelevated">
                    + Create Purchase
                </a>
            </div>

        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm">

            <div class="card-body p-3">

                <h5 class="fw-semibold mb-2">PURCHASE LIST</h5>
                <hr>

                <!-- RESPONSIVE TABLE -->
                <div class="table-responsive">
                    <table id="purchaseTable" class="table table-hover table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Purchase Code</th>
                                <th>Date</th>
                                <th>Supplier</th>
                                <th>Total</th>
                                <th>Balance</th>
                                <th>Created by</th>
                                <th>Created at</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

    @include('backend.purchase.modal')
@endsection



@push('footer-script')
    <script>
        window.PURCHASE_INDEX_ROUTE = "{{ route('purchase.index') }}";
        window.GET_PURCHASE_DATA_ROUTE = "{{ route('purchase.getData') }}";
        window.PURCHASE_MAKE_PAYMENT_ROUTE = "{{ route('purchase.makePayment', ':id') }}";
        window.PURCHASE_PAYMENT_HISTORY_ROUTE = "{{ route('purchase.paymentHistory') }}";

        window.PURCHASE_PAYMENT_DELETE_ROUTE = "{{ route('purchase.payment.delete') }}";

        window.PURCHASE_DELETE_ROUTE = "{{ route('purchase.delete') }}";
    </script>

    <script src="{{ asset('assets/backend/js/purchase.js') }}"></script>
@endpush
