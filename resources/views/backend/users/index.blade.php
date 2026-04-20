@extends('backend.layouts.app')
@section('title')
    Billing Software | Users
@endsection


@section('content')
    <div class="container-fluid">

        <!-- PAGE HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0 fw-semibold">
                Users Management
            </h5>

            <a href="{{ route('users.create') }}" class="mdc-button mdc-button--unelevated">
                + Add User
            </a>
        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-2">

                <table id="userTable" class="table table-hover mb-0" style="width:100%; table-layout: fixed;">
                    <thead class="bg-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Is Active</th>
                            <th>Active</th>
                        </tr>
                    </thead>

                    <tbody>

                    </tbody>

                </table>


            </div>
        </div>

    </div>
@endsection



@push('footer-script')
    <script>
        $(document).ready(function() {
            $('#userTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('users.index') }}",

                pagingType: "full_numbers",

                language: {
                    paginate: {
                        first: '«',
                        previous: '‹',
                        next: '›',
                        last: '»'
                    }
                },

                //ORACLE FIX
                ordering: false,
                pageLength: 25,

                columnDefs: [{
                    targets: '_all',
                    className: 'text-start'
                }],


                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'role',
                        name: 'role'
                    },
                    {
                        data: 'is_active',
                        name: 'is_active'
                    },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>

    <script>
        $(document).on('change', '.user-status-toggle', function() {

            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: "/users/update-status/" + id,
                type: "POST",
                data: {
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function() {
                    //$('#userTable').DataTable().ajax.reload(null, false);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: "Status Updated",
                        showConfirmButton: false,
                        timer: 2500
                    });
                }
            });

        });
    </script>
@endpush
