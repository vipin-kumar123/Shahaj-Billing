@extends('backend.layouts.app')
@section('title')
    Billing Software | Products
@endsection


@section('content')
    <div class="container-fluid">

        <!-- PAGE HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0 fw-semibold">
                Products
            </h5>

            <a href="{{ route('products.create') }}" class="mdc-button mdc-button--unelevated">
                + Add Item
            </a>
        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-2">

                <div class="table-responsive">
                    <table id="productTable" class="table table-hover table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Code No</th>
                                <th>Name</th>
                                <th>Brand</th>
                                <th>Unit</th>
                                <th>Purchase Price</th>
                                <th>Price</th>
                                <th>Item Type</th>
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
    </div>
@endsection


@push('footer-script')
    <script>
        window.PRODUCT_INDEX_ROUTE = "{{ route('products.index') }}";
        window.PRODUCT_DESTROY_ROUTE = "{{ route('products.delete') }}";
    </script>

    <script src="{{ asset('assets/backend/js/product.js') }}"></script>
@endpush
