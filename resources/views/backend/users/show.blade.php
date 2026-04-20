@extends('backend.layouts.app')
@section('title')
    Billing Software | Show User
@endsection


@section('content')
    <div class="container-fluid">

        <!-- PAGE HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0 fw-semibold">
                Edit Users
            </h5>

            <a href="{{ route('users.index') }}" class="mdc-button mdc-button--unelevated filled-button--secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm">
            <div class="card-body px-5 py-4">

                <!-- USER INFO SECTION -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">User Information</h6>

                    <div class="row mb-2">
                        <div class="col-md-3 fw-semibold">Name:</div>
                        <div class="col-md-9">{{ $user->name }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-3 fw-semibold">Email:</div>
                        <div class="col-md-9">{{ $user->email }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-3 fw-semibold">Phone:</div>
                        <div class="col-md-9">{{ $user->phone ?? 'N/A' }}</div>
                    </div>
                </div>


                <!-- ROLE SECTION -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Assigned Role</h6>

                    <div class="p-3 border rounded bg-light">
                        @if ($user->roles->count())
                            <span class="badge bg-primary px-3 py-2 fs-6">
                                {{ $user->roles->first()->name }}
                            </span>
                        @else
                            <span class="badge bg-secondary">No Role Assigned</span>
                        @endif
                    </div>
                </div>


                <!-- PERMISSION SECTION -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Permissions of This Role</h6>

                    <div class="p-3 border rounded bg-light">
                        @php
                            $permissions = $user->roles->first()?->permissions;
                        @endphp

                        @if ($permissions && $permissions->count())
                            @foreach ($permissions as $permission)
                                <span class="badge bg-success mb-2 me-2">
                                    {{ $permission->name }}
                                </span>
                            @endforeach
                        @else
                            <span class="badge bg-danger">No Permissions</span>
                        @endif
                    </div>
                </div>


                <!-- BUTTONS -->
                <div class="mt-4">
                    <a href="{{ route('users.edit', $user->id) }}"
                        class="mdc-button mdc-button--unelevated filled-button--success">
                        <i class="bi bi-pencil-square me-2"></i> Edit User
                    </a>

                    <a href="{{ route('users.index') }}" class="mdc-button mdc-button--unelevated filled-button--dark">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>

            </div>
        </div>


    </div>
@endsection




@push('footer-script')
@endpush
