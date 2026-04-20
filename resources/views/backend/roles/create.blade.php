@extends('backend.layouts.app')
@section('title')
    Billing Software | Add Role
@endsection


@section('content')
    <div class="container-fluid">

        <!-- PAGE HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0 fw-semibold">Add Role</h5>
            <a href="{{ route('roles.index') }}" class="mdc-button mdc-button--unelevated filled-button--secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm">

            <div class="card-body" style="min-height: 60vh;">

                <!-- GLOBAL VALIDATION ERRORS -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <!-- FORM -->
                <form method="post" action="{{ route('roles.store') }}" class="w-100">
                    @csrf

                    <!-- NAME -->
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-3 text-end">
                            <label class="form-label mb-0">Name</label>
                        </div>

                        <div class="col-md-6">
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control"
                                placeholder="Enter name">

                        </div>
                    </div>

                    <!-- PERMISSIONS -->
                    <div class="row mb-3">
                        <div class="col-md-3 d-flex justify-content-end align-items-start">
                            <label class="form-label mb-0 pt-1">Permissions</label>
                        </div>

                        <div class="col-md-6">

                            @forelse ($permissions as $permission)
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="permission_id[]" value="{{ $permission->name }}"
                                        class="form-check-input" id="permission_{{ $permission->id }}"
                                        {{ is_array(old('permission_id')) && in_array($permission->name, old('permission_id')) ? 'checked' : '' }}>

                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            @empty
                                <span class="text-muted">No permissions available</span>
                            @endforelse

                            @error('permission_id')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror

                        </div>

                    </div>

                    <!-- BUTTON -->
                    <div class="row mt-4">
                        <div class="col-md-3"></div>
                        <div class="col-md-6">
                            <button type="submit" class="mdc-button mdc-button--unelevated">
                                + Add Role
                            </button>
                        </div>
                    </div>

                </form>
                <!-- /FORM -->

            </div>
        </div>

    </div>
@endsection




@push('footer-script')
@endpush
