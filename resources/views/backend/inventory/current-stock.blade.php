@extends('backend.layouts.app')
@section('title')
    Billing Software | Current Stock
@endsection

@section('content')
    <div class="container-fluid px-2">

        <!-- BREADCRUMB -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">

            <!-- LEFT -->
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Inventory</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>Current Stock</span>
            </div>

        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm">

            <div class="card-body p-3">

                <h5 class="fw-semibold mb-2">CURRENT STOCK LIST</h5>
                <hr>

                <!-- RESPONSIVE TABLE -->
                <div class="table-responsive">
                    <table id="stockTable" class="table table-hover table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
@endsection



@push('footer-script')
    <script>
        window.INVENTORY_STOCK_ROUTE = "{{ route('inventory.stock') }}";
    </script>

    <script src="{{ asset('assets/backend/js/inventory.js') }}"></script>
@endpush
