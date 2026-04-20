@extends('backend.layouts.app')
@section('title')
    Billing Software | Show Products
@endsection


@section('content')
    <div class="container-fluid">

        <!-- PAGE HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0 fw-semibold">
                Show Item
            </h5>

            <a href="{{ route('products.index') }}" class="mdc-button mdc-button--unelevated filled-button--secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
        <!-- CARD -->
        <div class="card border-0 shadow-sm">
            <div class="card-body px-5 py-4">

                <div class="row">

                    <!-- LEFT COLUMN -->
                    <div class="col-md-6">

                        <h6 class="fw-bold mb-3">Product Details</h6>

                        <div class="mb-3">
                            <span>Product Name</span>
                            <div class="text-secondary">{{ $product->name }}</div>
                        </div>

                        <div class="mb-3">
                            <span>Category</span>
                            <div class="text-secondary">{{ $product->category?->name ?? '-' }}</div>
                        </div>

                        <div class="mb-3">
                            <span>Sub Category</span>
                            <div class="text-secondary">{{ $product->subcategory?->name ?? '-' }}</div>
                        </div>

                        <div class="mb-3">
                            <span>Brand</span>
                            <div class="text-secondary">{{ $product->brand?->name ?? '-' }}</div>
                        </div>

                        <div class="mb-3">
                            <span>Product Code</span>
                            <div class="text-secondary">{{ $product->product_code ?? '-' }}</div>
                        </div>

                        <div class="mb-3">
                            <span>Barcode</span>
                            <div class="text-secondary">{{ $product->barcode_code ?? '-' }}</div>
                        </div>

                        <div class="mb-3">
                            <span>HSN Code</span>
                            <div class="text-secondary">{{ $product->hsn_code ?? '-' }}</div>
                        </div>

                        <div class="mb-3">
                            <span>SKU</span>
                            <div class="text-secondary">{{ $product->sku ?? '-' }}</div>
                        </div>

                    </div>


                    <!-- RIGHT COLUMN -->
                    <div class="col-md-6">

                        <h6 class="fw-bold mb-3">Pricing & Stock</h6>

                        <div class="mb-3">
                            <span>Purchase Price</span>
                            <div class="text-secondary">₹{{ number_format($product->purchase_price, 2) }}</div>
                        </div>

                        <div class="mb-3">
                            <span>Wholesale Price</span>
                            <div class="text-secondary">₹{{ number_format($product->wholesale_price, 2) }}</div>
                        </div>

                        <div class="mb-3">
                            <span>Distributor Price</span>
                            <div class="text-secondary">₹{{ number_format($product->distributor_price, 2) }}</div>
                        </div>

                        <div class="mb-3">
                            <span>Selling Price</span>
                            <div class="text-secondary">₹{{ number_format($product->saleing_price, 2) }}</div>
                        </div>

                        <div class="mb-3">
                            <span>GST %</span>
                            <div class="text-secondary">{{ $product->gst_percentage }}%</div>
                        </div>

                        <div class="mb-3">
                            <span>Opening Stock</span>
                            <div class="text-secondary">{{ $product->opening_stock }}</div>
                        </div>

                        <div class="mb-3">
                            <span>Low Stock Alert</span>
                            <div class="text-secondary">{{ $product->low_stock_alert }}</div>
                        </div>

                        <div class="mb-3">
                            <span>Product Type</span>
                            <div class="text-secondary">
                                <span class="badge bg-primary">{{ ucfirst($product->product_type) }}</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <span>Status</span>
                            <div class="text-secondary">
                                @if ($product->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>


                <!-- FOOTER INFO -->
                <div class="row mt-4">

                    <div class="col-md-6">
                        <span>Added By</span>
                        <div class="text-secondary">{{ $product->user?->name ?? 'System' }}</div>
                    </div>

                    <div class="col-md-6">
                        <span>IP Address</span>
                        <div class="text-secondary">{{ $product->ip }}</div>
                    </div>

                </div>

            </div>
        </div>


    </div>
@endsection

@push('footer-script')
@endpush
