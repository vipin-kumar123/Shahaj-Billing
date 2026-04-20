@extends('backend.layouts.app')
@section('title')
    Billing Software | Brands
@endsection


@section('content')
    <div class="container-fluid">

        <!-- PAGE HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0 fw-semibold">
                Brands
            </h5>

            <a href="javascript:void(0)" id="addBrand" class="mdc-button mdc-button--unelevated">
                + Add Brand
            </a>
        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-2">
                <div class="table-response">
                    <table id="brandTable" class="table table-hover mb-0" style="width:100%; table-layout: fixed;">
                        <thead class="bg-light">
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
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
    @include('backend.brands.modal')
@endsection


@push('footer-script')
    <script>
        window.BRAND_INDEX_ROUTE = "{{ route('brands.index') }}";
        window.BRAND_STORE_ROUTE = "{{ route('brands.store') }}";
        window.BRAND_DELETE_ROUTE = "{{ route('brands.delete') }}";
        window.BRAND_EDIT_ROUTE = "{{ route('brands.edit') }}";
        window.BRAND_UPDATE_ROUTE = "{{ route('brands.update') }}";
        window.BRAND_SHOW_ROUTE = "{{ route('brands.show') }}";
    </script>

    <script src="{{ asset('assets/backend/js/brand.js') }}"></script>
@endpush
