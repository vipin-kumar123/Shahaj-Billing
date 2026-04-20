@extends('backend.layouts.app')
@section('title')
    Billing Software | Permissions
@endsection


@section('content')
    <div class="container-fluid">

        <!-- PAGE HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0 fw-semibold">
                Permissions Management
            </h5>

            <button class="mdc-button mdc-button--unelevated" data-bs-toggle="modal" data-bs-target="#permission">
                + Add Permissions
            </button>
        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-2">
                <table id="permissionTable" class="table table-hover mb-0" style="width:100%; table-layout: fixed;">
                    <thead class="bg-light">
                        <tr>
                            <th>Name</th>
                            <th>Action</th>

                        </tr>
                    </thead>

                    <tbody>

                    </tbody>

                </table>
            </div>
        </div>
    </div>
    @include('backend.permissions.modal')
@endsection


@push('footer-script')
    <script>
        $(document).ready(function() {
            $('#permissionTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('permissions.index') }}",

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

                columnDefs: [{
                    targets: '_all',
                    className: 'text-start'
                }],


                columns: [{
                        data: 'name',
                        name: 'name'
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
        $(document).on('submit', '#permissionForm', function(e) {
            e.preventDefault();

            let $btn = $("#permissionBtn");
            $btn.prop("disabled", true).text("Saving...");

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('permissions.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,

                success: function(res) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: res.message,
                    }).then((result) => {
                        // Form reset
                        $("#permissionForm")[0].reset();
                        // DataTable reload
                        $('#permissionTable').DataTable().ajax.reload(null, false);

                    });


                },

                error: function(err) {

                    if (err.status === 422) {

                        let errors = err.responseJSON.errors;
                        let errorText = "";

                        $.each(errors, function(key, value) {
                            errorText += value[0] + "<br>";
                        });

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error!',
                            html: errorText,
                        });

                    } else {

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong!'
                        });
                    }

                },

                complete: function() {
                    $btn.prop("disabled", false).text("Add Permission");
                }
            });
        });
    </script>
@endpush
