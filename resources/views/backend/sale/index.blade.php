@extends('backend.layouts.app')
@section('title')
    Billing Software | Sale List
@endsection

@section('content')
    <div class="container-fluid px-2">

        <!-- BREADCRUMB -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">

            <!-- LEFT -->
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Sale</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>Sale List</span>
            </div>

            <!-- RIGHT -->
            <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="{{ route('sale.create') }}" class="mdc-button mdc-button--unelevated">
                    + Create Sale
                </a>
            </div>

        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm">

            <div class="card-body p-3">

                <h5 class="fw-semibold mb-2">SALE LIST</h5>
                <hr>

                <!-- RESPONSIVE TABLE -->
                <div class="table-responsive">
                    <table id="saleTable" class="table table-hover table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Sale Code</th>
                                <th>Date</th>
                                <th>Customer</th>
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

    @include('backend.sale.modal')
@endsection



@push('footer-script')
    <script>
        window.SALE_INDEX_ROUTE = "{{ route('sale.index') }}";
        window.SALE_DELETE_ROUTE = "{{ route('sale.delete') }}";

        window.GET_SALE_DATA_ROUTE = "{{ route('sale.GetSaleData') }}";
        window.SALE_RECEIVE_PAYMENT_ROUTE = "{{ route('sale.receive.payment', ':id') }}";
        window.SALE_RECEIVE_HISTORY_ROUTE = "{{ route('sale.receive.history') }}";

        window.SALE_RECEIVE_PAYMENT_DELETE_ROUTE = "{{ route('sale.delete.receive.payment') }}";
    </script>

    <script src="{{ asset('assets/backend/js/sale.js') }}"></script>
@endpush
