@extends('backend.layouts.app')
@section('title')
    Billing Software | Categories
@endsection


@section('content')
    <div class="container-fluid">

        <!-- PAGE HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0 fw-semibold">
                Categories
            </h5>

            <a href="javascript:void(0)" id="addcat" class="mdc-button mdc-button--unelevated">
                + Add Category
            </a>
        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-2">

                <table id="categoriesTable" class="table table-hover mb-0" style="width:100%; table-layout: fixed;">
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
    @include('backend.category.modal')
@endsection


@push('footer-script')
    <script>
        window.CATEGORY_INDEX_ROUTE = "{{ route('categories.index') }}";
        window.CATEGORY_STORE_ROUTE = "{{ route('categories.store') }}";
        window.CATEGORY_STATUS_ROUTE = "{{ route('categories.status.update') }}";
        window.CATEGORY_EDIT_ROUTE = "{{ route('categories.edit') }}";
        window.CATEGORY_UPDATE_ROUTE = "{{ route('categories.update') }}";
        window.CATEGORY_SHOW_ROUTE = "{{ route('categories.show') }}";
        window.CATEGORY_DELETE_ROUTE = "{{ route('categories.delete') }}";
    </script>

    <script src="{{ asset('assets/backend/js/category.js') }}"></script>
@endpush
