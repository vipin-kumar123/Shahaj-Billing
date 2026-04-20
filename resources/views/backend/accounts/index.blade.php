@extends('backend.layouts.app')
@section('title')
    Billing Software | Accounts
@endsection


@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Accounts</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>List</span>
                {{-- <i class="bi bi-chevron-right mx-2"></i>
                <span class="fw-semibold text-dark">Create Purchase</span> --}}
            </div>

            <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="javascript:avoid(0)" class="mdc-button mdc-button--unelevated" data-bs-toggle="modal"
                    data-bs-target="#addAccountModal">
                    <i class="bi bi-plus"></i> Add Account
                </a>
            </div>
        </div>


        <!-- CARD -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-2">

                <table id="accountTable" class="table table-hover mb-0" style="width:100%; table-layout: fixed;">
                    <thead class="bg-light">
                        <tr>
                            <th>Account Name</th>
                            <th>Type</th>
                            <th>Parent</th>
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
    @include('backend.accounts.modal')
@endsection


@push('footer-script')
    <script>
        window.ACCOUNT_INDEX_ROUTE = "{{ route('accounts.index') }}";
        window.ACCOUNT_STORE_ROUTE = "{{ route('accounts.store') }}";
        window.ACCOUNT_EDIT_ROUTE = "{{ route('accounts.edit') }}";
        window.ACCOUNT_UPDATE_BASE_ROUTE = "{{ url('accounts/update') }}";
        window.ACCOUNT_DELETE_ROUTE = "{{ route('accounts.destroy') }}";
    </script>

    <script src="{{ asset('assets/backend/js/accounts.js') }}"></script>
@endpush
