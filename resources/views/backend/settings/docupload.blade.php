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
                                href="#doc" role="tab">
                                <i class="bi bi-file-earmark-text me-2"></i> Doc Conver to Excel
                            </a>

                            <a class="nav-link py-3 border-bottom" id="v-logo-tab" data-bs-toggle="pill" href="#pdf"
                                role="tab">
                                <i class="bi bi-file-pdf me-2 text-danger"></i> Doc Convert to PDF
                            </a>

                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE CONTENT -->
            <div class="col-md-9">
                <div class="tab-content shadow-sm card p-4" id="v-tabs-content">

                    <div class="tab-pane fade show active" id="doc" role="tabpanel">

                        <div class="card-header bg-light">
                            <h4>Upload DOCX File</h4>
                        </div>
                        <div class="card-body">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <form action="{{ route('doc.convert') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Select DOCX File</label>
                                    <input type="file" name="doc_file" class="form-control" required>
                                </div>

                                <button type="submit" class="mdc-button mdc-button--unelevated px-5 mt-2">
                                    Convert to Excel
                                </button>
                            </form>

                        </div>


                    </div>


                    <div class="tab-pane fade" id="pdf" role="tabpanel">

                        <div class="card-header bg-light">
                            <h4>Upload PDF File</h4>
                        </div>
                        <div class="card-body">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <form action="{{ route('doc.convert') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Select PDF File</label>
                                    <input type="file" name="pdf_file" class="form-control" required>
                                </div>

                                <button type="submit" class="mdc-button mdc-button--unelevated px-5 mt-2">
                                    Convert to PDF
                                </button>
                            </form>

                        </div>


                    </div>




                </div>
            </div>

        </div>

    </div>
@endsection



@push('footer-script')
@endpush
