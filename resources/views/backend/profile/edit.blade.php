@extends('backend.layouts.app')
@section('title')
    Billing Software | Profile
@endsection


@section('content')
    <div class="container-fluid px-2">

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Profile</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>Update Profile</span>

            </div>

        </div>

        <div class="row">
            <!-- LEFT VERTICAL TAB MENU -->
            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <h6 class="p-3 m-0 border-bottom fw-bold">Options</h6>

                        <div class="nav flex-column nav-pills" id="v-tabs" role="tablist">

                            <a class="nav-link active py-3 border-bottom" id="v-profile-tab" data-bs-toggle="pill"
                                href="#v-profile" role="tab">
                                <i class="bi bi-person me-2"></i> Profile
                            </a>

                            <a class="nav-link py-3 border-bottom" id="v-password-tab" data-bs-toggle="pill"
                                href="#v-password" role="tab">
                                <i class="bi bi-lock me-2"></i> Change Password
                            </a>

                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE CONTENT -->
            <div class="col-md-9">
                <div class="tab-content shadow-sm card p-4" id="v-tabs-content">

                    <!-- PROFILE TAB -->
                    <div class="tab-pane fade show active" id="v-profile" role="tabpanel">

                        <h5 class="fw-bold mb-3">Profile</h5>

                        <form method="post" id="profileForm" action="{{ route('profile.update') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <!-- PROFILE PICTURE SECTION -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Profile Picture</label>

                                <div class="d-flex align-items-center gap-4">

                                    <!-- Image Box -->
                                    <div style="width: 100px; height: 100px; border-radius: 6px; background: #f1f1f1;"
                                        class="d-flex justify-content-center align-items-center overflow-hidden">
                                        @if (!empty($user->photo))
                                            <img id="previewImage" src="{{ asset($user->photo) }}"
                                                data-default="{{ asset('assets/backend/images/faces/default.png') }}"
                                                style="width: 80px; opacity: 0.8;">
                                        @else
                                            <img id="previewImage"
                                                src="{{ asset('assets/backend/images/faces/default.png') }}"
                                                data-default="{{ asset('assets/backend/images/faces/default.png') }}"
                                                style="width: 80px; opacity: 0.8;">
                                        @endif

                                    </div>

                                    <!-- Buttons + Text -->
                                    <div>

                                        <div class="d-flex gap-2">
                                            <label class="btn btn-outline-primary btn-sm m-0">
                                                Browse
                                                <input type="file" id="photoInput" name="photo" accept="image/*"
                                                    class="d-none">
                                            </label>

                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="resetImage">
                                                Reset
                                            </button>
                                        </div>

                                        <small class="text-muted mt-1 d-block">
                                            Allowed JPG, GIF or PNG. Max size of 1MB
                                        </small>

                                    </div>

                                </div>
                            </div>

                            <!-- OTHER PROFILE FIELDS -->
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control mb-3"
                                value="{{ old('name', $user->name ?? '') }}" placeholder="Full Name">

                            <label class="form-label">Email</label>
                            <input type="text" name="email" class="form-control mb-3"
                                value="{{ old('email', $user->email ?? '') }}" placeholder="Email">

                            <label class="form-label">Mobile</label>
                            <input type="text" name="phone" class="form-control mb-3"
                                value="{{ old('phone', $user->phone ?? '') }}" placeholder="Mobile">

                            <button type="submit" id="profileBtn"
                                class="mdc-button mdc-button--unelevated px-5 mt-2">Save</button>

                        </form>

                    </div>


                    <!-- CHANGE PASSWORD TAB -->
                    <div class="tab-pane fade" id="v-password" role="tabpanel">

                        <h5 class="fw-bold mb-3">Change Password</h5>

                        <form method="post" id="passwordForm">
                            <label class="form-label">Old Password</label>
                            <input type="password" name="old_password" class="form-control mb-3">

                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control mb-3">

                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control mb-3">

                            <button type="submit" id="passwordBtn"
                                class="mdc-button mdc-button--unelevated px-5 mt-2">Update</button>
                        </form>

                    </div>

                </div>
            </div>

        </div>

    </div>
@endsection



@push('footer-script')
    <script>
        window.PROFILE_UPDATE = "{{ route('profile.update') }}";
        window.PROFILE_PASSWORD_CHANGE = "{{ route('profile.changePassword') }}";
    </script>

    <script src="{{ asset('assets/backend/js/profile.js') }}"></script>
@endpush
