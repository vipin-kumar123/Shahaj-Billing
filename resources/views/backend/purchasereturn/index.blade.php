@extends('backend.layouts.app')
@section('title')
    Billing Software | Purchase Return List
@endsection

@section('content')
    <div class="container-fluid px-2">

        <!-- BREADCRUMB -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">

            <!-- LEFT -->
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Purchase Return</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>Purchase Return List</span>
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

                <h5 class="fw-semibold mb-2">PURCHASE RETURN LIST</h5>
                <hr>

                <!-- RESPONSIVE TABLE -->
                <div class="table-responsive">
                    <table id="purchaseReturn" class="table table-hover table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Return ID</th>
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

    @include('backend.purchasereturn.modal')
@endsection



@push('footer-script')
    <script>
        window.PURCHASE_RETURN_INDEX_ROUTE = "{{ route('purchase.return.index') }}";
        window.GET_PURCHASE_RETURN_DATA = "{{ route('purchase.return.GetPurchaseReturnData') }}";
        window.MAKE_PAYMENT_PURCHASE_RETURN = "{{ route('purchase.return.purchaseReturnPayment', ':id') }}";

        // FIXED ROUTE
        window.PURCHASE_RETURN_PAYMENT_HISTORY = "{{ route('purchase.return.payment.history') }}";

        window.DELETE_RETURN_PAYMENT_HISTORY = "{{ route('purchase.return.delete.payment') }}";
    </script>


    <script src="{{ asset('assets/backend/js/purchasereturn.js') }}"></script>
@endpush
