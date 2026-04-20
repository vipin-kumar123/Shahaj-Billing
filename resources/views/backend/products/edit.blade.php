@extends('backend.layouts.app')
@section('title')
    Billing Software | Edit Products
@endsection
@php
    $units = ['PCS', 'KG', 'L', 'BOX', 'PACKET'];
    $product_type = ['simple', 'variant', 'service'];
@endphp

@section('content')
    <div class="container-fluid">

        <!-- PAGE HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0 fw-semibold">
                Edit Item
            </h5>

            <a href="{{ route('products.index') }}" class="mdc-button mdc-button--unelevated filled-button--secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm">
            <div class="card-body px-5 py-4">

                <form method="post" action="{{ route('products.update', $item->id) }}" class="w-100">
                    @csrf
                    @method('PUT')
                    <!-- CATEGORY -->
                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">Category</label>
                        <div class="col-md-6">
                            <select name="category_id" id="category_id"
                                class="form-control select2 @error('category_id') is-invalid @elseif(old('category_id')) is-valid @enderror"
                                data-placeholder="Nothing select" data-selected="{{ $item->category_id }}">
                                <option value="">Select Category</option>
                                @isset($categories)
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ old('category_id', $item->category_id ?? null) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            @error('category_id')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- SUB CATEGORY -->
                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">Sub Category</label>
                        <div class="col-md-6">
                            <select name="sub_category_id" id="sub_category_id" class="form-control select2"
                                data-placeholder="Nothing select" data-selected="{{ $item->sub_category_id }}">
                                {{-- append sub category --}}
                            </select>
                            @error('sub_category_id')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- BRAND -->
                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">Brand</label>
                        <div class="col-md-6">
                            <select name="brand_id" class="form-control select2" data-placeholder="Nothing select">
                                <option value="">Select Brand</option>
                                @isset($brands)
                                    @foreach ($brands as $b)
                                        <option value="{{ $b->id }}"
                                            {{ old('brand_id', $item->brand_id ?? null) == $b->id ? 'selected' : '' }}>
                                            {{ $b->name }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            @error('brand_id')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- PRODUCT NAME -->
                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">Product Name</label>
                        <div class="col-md-6">
                            <input type="text" name="name"
                                class="form-control @error('name') is-invalid @elseif(old('name')) is-valid @enderror"
                                value="{{ old('name', $item->name) }}" placeholder="Product Name">
                            @error('name')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- SLUG -->

                    <!-- BARCODE -->
                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">Barcode (Optional)</label>
                        <div class="col-md-6">
                            <input type="text" name="barcode_code" class="form-control"
                                value="{{ old('barcode_code', $item->barcode_code) }}"
                                placeholder="Scan barcode or leave empty">
                            @error('barcode_code')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- HSN -->
                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">HSN Code</label>
                        <div class="col-md-6">
                            <input type="text" name="hsn_code" class="form-control"
                                value="{{ old('hsn_code', $item->hsn_code) }}" placeholder="HSN Code">
                            @error('hsn_code')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">SKU</label>
                        <div class="col-md-6">
                            <input type="text" name="sku" class="form-control" placeholder="SKU"
                                value="{{ old('sku', $item->sku) }}">
                            @error('sku')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- UNIT -->
                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">Unit</label>
                        <div class="col-md-6">
                            <select name="unit" class="form-control">
                                <option value="">Select Unit</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit }}"
                                        {{ old('unit', $item->unit) == $unit ? 'selected' : '' }}>
                                        {{ $unit }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unit')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- PURCHASE PRICE -->
                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">Purchase Price</label>
                        <div class="col-md-6">
                            <input type="number" step="0.01" name="purchase_price" class="form-control"
                                value="{{ old('purchase_price', $item->purchase_price) }}">
                            @error('purchase_price')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- WHOLESALE PRICE -->
                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">Wholesale Price</label>
                        <div class="col-md-6">
                            <input type="number" step="0.01" name="wholesale_price" class="form-control"
                                value="{{ old('wholesale_price', $item->wholesale_price) }}">
                            @error('wholesale_price')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">Distributor Price</label>
                        <div class="col-md-6">
                            <input type="number" step="0.01" name="distributor_price" class="form-control"
                                value="{{ old('distributor_price', $item->distributor_price) }}">
                            @error('distributor_price')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- SELLING PRICE -->
                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">Selling Price</label>
                        <div class="col-md-6">
                            <input type="number" step="0.01" name="saleing_price" class="form-control"
                                value="{{ old('saleing_price', $item->saleing_price) }}">
                            @error('saleing_price')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- GST -->
                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">GST %</label>
                        <div class="col-md-6">
                            <input type="number" step="0.01" name="gst_percentage" class="form-control"
                                value="{{ old('gst_percentage', $item->gst_percentage) }}" placeholder="0 – 28">
                            @error('gst_percentage')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- OPENING STOCK -->
                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">Opening Stock</label>
                        <div class="col-md-6">
                            <input type="number" name="opening_stock" class="form-control"
                                value="{{ old('opening_stock', $item->opening_stock ?? 0) }}">
                            @error('opening_stock')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- LOW STOCK ALERT -->
                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">Low Stock Alert</label>
                        <div class="col-md-6">
                            <input type="number" name="low_stock_alert" class="form-control"
                                value="{{ old('low_stock_alert', $item->low_stock_alert ?? 0) }}">
                            @error('low_stock_alert')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- PRODUCT TYPE -->
                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">Product Type</label>
                        <div class="col-md-6">
                            <select name="product_type" class="form-control">
                                @foreach ($product_type as $type)
                                    <option value="{{ $type }}"
                                        {{ old('product_type', $item->product_type) == $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_type')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- SUBMIT -->
                    <div class="row mt-4">
                        <div class="col-md-3"></div>
                        <div class="col-md-6">
                            <button type="submit" class="mdc-button mdc-button--unelevated w-100">
                                <i class="bi bi-pen"></i> Update Product
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>


    </div>
@endsection

@push('footer-script')
    <script>
        window.PRODUCT_GET_SUBCATEGORIES = "{{ route('products.subcategories') }}";
    </script>

    <script src="{{ asset('assets/backend/js/product.js') }}"></script>
@endpush
