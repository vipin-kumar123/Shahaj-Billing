@extends('backend.layouts.app')
@section('title')
    Billing Software | Sub Categories
@endsection


@section('content')
    <div class="container-fluid">

        <!-- PAGE HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0 fw-semibold">
                Sub Categories
            </h5>

            <a href="javascript:void(0)" id="addSubCat" class="mdc-button mdc-button--unelevated">
                + Add Sub Category
            </a>
        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-2">

                <table id="subcategoryTable" class="table table-hover mb-0" style="width:100%; table-layout: fixed;">
                    <thead class="bg-light">
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Category</th>
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
    @include('backend.subcategory.modal')
@endsection


@push('footer-script')
    <script>
        window.SUBCATEGORY_INDEX_ROUTE = "{{ route('subcategories.index') }}";
        window.SUBCATEGORY_STORE_ROUTE = "{{ route('subcategories.store') }}";
        window.SUBCATEGORY_STATUS_ROUTE = "{{ route('subcategories.update.status') }}";
        window.SUBCATEGORY_EDIT_ROUTE = "{{ route('subcategories.edit') }}";
        window.SUBCATEGORY_UPDATE_ROUTE = "{{ route('subcategories.update') }}";
        window.SUBCATEGORY_SHOW_ROUTE = "{{ route('subcategories.show') }}";
        window.SUBCATEGORY_DELETE_ROUTE = "{{ route('subcategories.delete') }}";
    </script>

    <script src="{{ asset('assets/backend/js/subcategory.js') }}"></script>
@endpush
