@extends('backend.layouts.app')
@section('title')
    Billing Software | Add User
@endsection


@section('content')
    <div class="container-fluid">

        <!-- PAGE HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0 fw-semibold">
                Add Users
            </h5>

            <a href="{{ route('users.index') }}" class="mdc-button mdc-button--unelevated filled-button--secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm">
            <div class="card-body px-5 py-4">

                <form method="post" action="{{ route('users.store') }}" class="w-100">
                    @csrf

                    <!-- NAME -->
                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">Name</label>
                        <div class="col-md-6">
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @elseif(old('name')) is-valid @enderror"
                                placeholder="Enter full name">

                            @error('name')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- EMAIL -->
                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">Email</label>
                        <div class="col-md-6">
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @elseif(old('email')) is-valid @enderror"
                                placeholder="Enter email address">

                            @error('email')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- PHONE -->
                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">Phone</label>
                        <div class="col-md-6">
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                class="form-control @error('phone') is-invalid @elseif(old('phone')) is-valid @enderror"
                                placeholder="Enter phone number">

                            @error('phone')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">Password</label>
                        <div class="col-md-6">
                            <input type="password" name="password" value="{{ old('password') }}"
                                class="form-control @error('password') is-invalid @elseif(old('password')) is-valid @enderror"
                                placeholder="****">

                            @error('password')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">Confirm Password</label>
                        <div class="col-md-6">
                            <input type="password" name="password_confirmation" value="{{ old('password_confirmation') }}"
                                class="form-control @error('password_confirmation') is-invalid @elseif(old('password_confirmation')) is-valid @enderror"
                                placeholder="****">
                        </div>
                    </div>

                    <!-- ROLE -->
                    <div class="mb-3 row">
                        <label class="col-md-3 col-form-label text-md-end fw-semibold">Role</label>
                        <div class="col-md-6">
                            <select name="role_id"
                                class="form-select @error('role_id') is-invalid @elseif(old('role_id')) is-valid @enderror">
                                <option value="">Select role</option>

                                @forelse ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @empty
                                    <option value="">Role Not Available</option>
                                @endforelse
                            </select>

                            @error('role_id')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror

                        </div>
                    </div>

                    <!-- BUTTON -->
                    <div class="row mt-4">
                        <div class="col-md-3"></div>
                        <div class="col-md-6">
                            <button type="submit" class="mdc-button mdc-button--unelevated px-4 py-2 w-100">
                                + Add User
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>


    </div>
@endsection




@push('footer-script')
@endpush
