@extends('backend.layouts.app')
@section('title')
    Billing Software | Show Role
@endsection


@section('content')
    <div class="container-fluid">

        <!-- PAGE HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0 fw-semibold">Show Role</h5>
            <a href="{{ route('roles.index') }}" class="mdc-button mdc-button--unelevated filled-button--secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm">

            <div class="card-body" style="min-height: 60vh;">

                <div class="row">

                    <!-- ROLE INFO -->
                    <div class="col-md-4 border-end">
                        <h6 class="fw-bold mb-3">Role Information</h6>

                        <p>
                            <strong>Role Name:</strong><br>
                            <span class="badge bg-primary fs-6 mt-1">
                                {{ $role->name }}
                            </span>
                        </p>

                        <p class="mt-3">
                            <strong>Guard:</strong><br>
                            <span class="text-muted">{{ $role->guard_name }}</span>
                        </p>

                        <p class="mt-3">
                            <strong>Total Permissions:</strong><br>
                            <span class="badge bg-dark">
                                {{ $role->permissions->count() }}
                            </span>
                        </p>
                    </div>


                    <!-- PERMISSIONS -->
                    <div class="col-md-8">
                        <h6 class="fw-bold mb-3">Permissions Assigned</h6>

                        @if ($role->permissions->count())
                            @php
                                // Group permissions by prefix (user-create → user)
                                $groupedPermissions = $role->permissions->groupBy(function ($permission) {
                                    return explode('-', $permission->name)[0];
                                });
                            @endphp

                            @foreach ($groupedPermissions as $group => $permissions)
                                <div class="mb-3">
                                    <div class="fw-semibold text-uppercase mb-2 text-secondary">
                                        {{ $group }}
                                    </div>

                                    @foreach ($permissions as $permission)
                                        <span class="badge bg-success me-1 mb-1">
                                            {{ $permission->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endforeach
                        @else
                            <span class="badge bg-danger">No Permissions Assigned</span>
                        @endif
                    </div>

                </div>


            </div>
        </div>

    </div>
@endsection




@push('footer-script')
@endpush
