@extends('backend.layouts.app')
@section('title')
    Billing Software | Create Customer
@endsection


@section('content')
    <div class="container-fluid px-2">

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">

            <!-- LEFT -->
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Contacts</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>Customer List</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span class="fw-semibold text-dark">Create Customer</span>
            </div>

            <!-- RIGHT -->
            <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="{{ route('customers.index') }}" class="mdc-button mdc-button--danger btn-sm">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

        </div>



        <!-- CARD -->
        <div class="card border-0 shadow-sm">
            <div class="card-body px-5 py-3">

                <form method="POST" action="{{ route('customers.store') }}">
                    @csrf

                    <!-- CUSTOMER TYPE -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Customer Type</label>
                        <div>
                            <label class="me-4">
                                <input type="radio" name="customer_type" value="retailer" checked> Retailer
                            </label>
                            <label class="me-4">
                                <input type="radio" name="customer_type" value="wholesale"> Wholesaler
                            </label>
                            <label>
                                <input type="radio" name="customer_type" value="distributor"> Distributor
                            </label>
                        </div>
                        @error('customer_type')
                            <small class="text-danger">{{ $message }} </small>
                        @enderror
                    </div>

                    <!-- PERSONAL INFO -->
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name"
                                class="form-control @error('first_name') is-invalid @enderror"
                                value="{{ old('first_name') }}">
                            @error('first_name')
                                <small class="text-danger">{{ $message }} </small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" name="mobile_number"
                                class="form-control @error('mobile_number') is-invalid @enderror"
                                value="{{ old('mobile_number') }}">
                            @error('mobile_number')
                                <small class="text-danger">{{ $message }} </small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Alternate Number</label>
                            <input type="text" name="alternate_number" class="form-control"
                                value="{{ old('alternate_number') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">WhatsApp Mobile</label>
                            <input type="text" name="whatsapp_number" class="form-control"
                                value="{{ old('whatsapp_number') }}">
                        </div>

                    </div>

                    <!-- TABS -->
                    <ul class="nav nav-tabs mt-4" id="custTabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#addressTab">
                                <i class="bi bi-geo-alt"></i> Address
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#businessTab">
                                <i class="bi bi-building"></i> Business
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#creditTab">
                                <i class="bi bi-cash"></i> Credit & Udhar
                            </a>
                        </li>
                    </ul>

                    <!-- TAB CONTENT -->
                    <div class="tab-content p-3 border border-top-0 rounded-bottom">

                        <!-- ADDRESS TAB -->
                        <div class="tab-pane fade show active" id="addressTab">

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">Village</label>
                                    <input type="text" name="village" class="form-control" value="{{ old('village') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Mohalla</label>
                                    <input type="text" name="mohalla" class="form-control"
                                        value="{{ old('mohalla') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">District</label>
                                    <input type="text" name="district" class="form-control"
                                        value="{{ old('district') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Area</label>
                                    <input type="text" name="area" class="form-control"
                                        value="{{ old('area') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">State</label>
                                    <select name="state" id="state_id" class="form-control select2">
                                        <option value="0">Nothing Select</option>
                                        @forelse ($state as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @empty
                                            <option value="0">No State Found</option>
                                        @endforelse
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">City</label>
                                    <select name="city" id="city_id" class="form-control select2">
                                        {{-- append city --}}
                                    </select>
                                </div>



                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Pincode</label>
                                    <input type="text" name="pincode" class="form-control"
                                        value="{{ old('pincode') }}">
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Billing Address</label>
                                    <textarea name="billing_address" class="form-control">{{ old('billing_address') }} </textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Shipping Address</label>
                                    <textarea name="shipping_address" class="form-control">{{ old('shipping_address') }}</textarea>
                                </div>
                            </div>

                        </div>

                        <!-- BUSINESS TAB -->
                        <div class="tab-pane fade" id="businessTab">
                            <div class="row g-3">

                                <div class="col-md-3">
                                    <label class="form-label">Is Business?</label><br>
                                    <input type="checkbox" name="is_business" value="1">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Business Name</label>
                                    <input type="text" name="business_name" class="form-control"
                                        value="{{ old('business_name') }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">GST Number</label>
                                    <input type="text" name="gst_number" class="form-control"
                                        value="{{ old('gst_number') }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">PAN Number</label>
                                    <input type="text" name="pan_number" class="form-control"
                                        value="{{ old('pan_number') }}">
                                </div>

                            </div>
                        </div>

                        <!-- CREDIT TAB -->
                        <div class="tab-pane fade" id="creditTab">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">Opening Balance</label>
                                    <input type="number" name="opening_balance" class="form-control"
                                        value="{{ old('opening_balance', 0) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Udhar Limit</label>
                                    <input type="number" name="udhar_limit" class="form-control"
                                        value="{{ old('udhar_limit', 0) }}">
                                </div>

                            </div>
                        </div>

                    </div>

                    <!-- SUBMIT -->
                    <div class="mt-4">
                        <button type="submit" class="mdc-button mdc-button--unelevated px-5">Submit</button>
                        <a href="{{ route('customers.index') }}" class="btn btn-light">Close</a>
                    </div>
                </form>

            </div>
        </div>

    </div>
@endsection


@push('footer-script')
    <script>
        window.CUSTOMER_STATE_GET_CITY = "{{ route('customers.stateGetCity') }}";
    </script>

    <script src="{{ asset('assets/backend/js/customer.js') }}"></script>
@endpush
