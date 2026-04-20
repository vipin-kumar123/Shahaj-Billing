@extends('backend.layouts.app')
@section('title')
    Billing Software | Sale Return List
@endsection

@section('content')
    <div class="container-fluid px-2">

        <!-- BREADCRUMB -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">

            <!-- LEFT -->
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Sale Return</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>Sale Return List</span>
            </div>

            <!-- RIGHT -->
            {{-- <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="#" class="mdc-button mdc-button--outlined">Import</a>

                <a href="" class="mdc-button mdc-button--unelevated">
                    + Create Purchase
                </a>
            </div> --}}

        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm">

            <div class="card-body p-3">

                <h5 class="fw-semibold mb-2">SALE RETURN LIST</h5>
                <hr>

                <!-- RESPONSIVE TABLE -->
                <div class="table-responsive">
                    <table id="saleReturn" class="table table-hover table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Return ID</th>
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

    @include('backend.salereturn.modal')
@endsection



@push('footer-script')
    <script>
        window.SALE_RETURN_INDEX_ROUTE = "{{ route('sale.return.index') }}";

        window.GET_SALE_RETURN_DATA = "{{ route('sale.return.getData') }}";

        window.MAKE_REFUND_PAYMENT_ROUTE = "{{ route('sale.return.refundPayment', ':id') }}";

        window.SALE_RETURN_REFUND_HISTORY_ROUTE = "{{ route('sale.return.refundHistory') }}";

        window.DELETE_SALE_RETURN_REFUND_ROUTE = "{{ route('sale.return.delete.refund') }}";
    </script>

    <script src="{{ asset('assets/backend/js/sale.js') }}"></script>
@endpush
