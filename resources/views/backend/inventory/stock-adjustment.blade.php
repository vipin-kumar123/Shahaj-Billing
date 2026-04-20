@extends('backend.layouts.app')
@section('title')
    Billing Software | Stock Adjustment
@endsection

@section('content')
    <div class="container-fluid px-2">

        <!-- BREADCRUMB -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">

            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Inventory</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>Stock Adjustment</span>
            </div>

        </div>

        <div class="card border-0 shadow-sm">

            <div class="card-body p-3">

                <h5 class="fw-semibold mb-2">STOCK ADJUSTMENT</h5>
                <hr>
                @if (session('warning'))
                    <div class="alert alert-warning">
                        {{ session('warning') }}
                    </div>
                @endif
                <form method="post" action="{{ route('inventory.adjustment.store') }}">
                    @csrf
                    <div class="row">

                        <!-- Product -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Product</label>
                            <select name="product_id" id="product_id" class="form-control">
                                <option value="">Select Product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Current Stock -->
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Current Stock</label>
                            <input type="text" id="current_stock" class="form-control bg-light" readonly>
                            <input type="hidden" name="current_stock" id="current_stock_hidden">
                        </div>

                        <!-- Adjustment Quantity -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Adjustment Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control"
                                placeholder="+5 or -3">

                            <small class="text-muted">
                                Use + for increase and - for decrease
                            </small>
                        </div>

                        <!-- New Stock Preview -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label">New Stock</label>
                            <input type="text" id="new_stock" name="new_stock" class="form-control bg-light" readonly>
                            @error('new_stock')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Adjust Stock
                    </button>

                </form>
            </div>
        </div>

    </div>
@endsection


@push('footer-script')
    <script>
        window.INVENTORY_GETSTOCKPRODUCT_ROUTE = "{{ route('inventory.get.product.stock') }}";
    </script>

    <script src="{{ asset('assets/backend/js/inventory.js') }}"></script>
@endpush
