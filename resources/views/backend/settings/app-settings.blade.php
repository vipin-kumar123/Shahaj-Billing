@extends('backend.layouts.app')
@section('title')
    Billing Software | Profile
@endsection


@section('content')
    <div class="container-fluid px-2">

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Settings</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>App Settings</span>

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
                                href="#app-setting" role="tab">
                                <i class="bi bi-file-earmark-text me-2"></i> General
                            </a>

                            <a class="nav-link py-3 border-bottom" id="v-logo-tab" data-bs-toggle="pill" href="#app-logo"
                                role="tab">
                                <i class="bi bi-images me-2"></i> Application Logo
                            </a>

                            <a class="nav-link py-3 border-bottom" id="v-company-tab" data-bs-toggle="pill" href="#company"
                                role="tab">
                                <i class="bi bi-building me-2"></i> Company / Shop Details
                            </a>

                            <a class="nav-link py-3 border-bottom" id="v-emailsetting-tab" data-bs-toggle="pill"
                                href="#email-settings" role="tab">
                                <i class="bi bi-envelope me-2"></i> Email Settings
                            </a>

                            <a class="nav-link py-3 border-bottom" id="v-sms-tab" data-bs-toggle="pill" href="#sms-settings"
                                role="tab">
                                <i class="bi bi-chat-text me-2"></i> SMS Settings
                            </a>

                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE CONTENT -->
            <div class="col-md-9">
                <div class="tab-content shadow-sm card p-4" id="v-tabs-content">

                    <div class="tab-pane fade show active" id="app-setting" role="tabpanel">

                        @include('backend.settings.partials.general')

                    </div>


                    <div class="tab-pane fade" id="app-logo" role="tabpanel">

                        @include('backend.settings.partials.app-logo')

                    </div>


                    <div class="tab-pane fade" id="company" role="tabpanel">

                        @include('backend.settings.partials.company')

                    </div>

                    <div class="tab-pane fade" id="email-settings" role="tabpanel">

                        @include('backend.settings.partials.email-setting')

                    </div>

                    <div class="tab-pane fade" id="sms-settings" role="tabpanel">

                        @include('backend.settings.partials.sms-setting')

                    </div>

                </div>
            </div>

        </div>

    </div>
@endsection



@push('footer-script')
    <script>
        window.ASSET = "{{ asset('') }}";
        window.GENERAL_SETTINGS_GET_DATA = "{{ route('setting.getGeneralData') }}";
        window.GENERAL_SETTINGS_UPDATE = "{{ route('setting.General') }}";
        window.LOGO_SETTINGS_UPDATE = "{{ route('setting.logo.update') }}";
        window.DEFAULT_IMAGE = "{{ asset('assets/backend/images/faces/default.png') }}";

        /***********************company details*****************************************/
        window.GET_COMPANY_DATA = "{{ route('setting.company.getAll.data') }}";
        window.COMPANY_GET_CITY_DATA = "{{ route('setting.company.getCities') }}";
        window.COMPANY_STORE_DATA = "{{ route('setting.company.store') }}";
        /***********************company details*****************************************/
    </script>

    <script src="{{ asset('assets/backend/js/setting.js') }}"></script>
    <script src="{{ asset('assets/backend/js/company.js') }}"></script>
@endpush
