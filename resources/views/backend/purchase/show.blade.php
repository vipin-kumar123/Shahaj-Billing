@extends('backend.layouts.app')
@section('title')
    Billing Software | Purchase Details
@endsection


@section('content')
    <div class="container-fluid px-2">

        <!-- BREADCRUMB -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Purchase</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>Purchase Bills</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span class="fw-semibold text-dark">Purchase </span>
            </div>

            <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="{{ route('purchase.return.create', $purchase->id) }}"
                    class="mdc-button mdc-button--outlined outlined-button--success mdc-ripple-upgraded">
                    <i class="bi bi-arrow-left-right me-1"></i> Convert to Return
                </a>
                <a href="{{ route('purchase.edit', $purchase->id) }}"
                    class="mdc-button mdc-button--outlined outlined-button--primary mdc-ripple-upgraded">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
                <button onclick="window.open('{{ route('purchase.print', $purchase->id) }}', '_blank')"
                    class="mdc-button mdc-button--outlined outlined-button--dark">
                    <i class="bi bi-printer me-1"></i> Print
                </button>

                <a href="{{ route('purchase.pdf', $purchase->id) }}"
                    class="mdc-button mdc-button--outlined outlined-button--secondary mdc-ripple-upgraded">
                    <i class="bi bi-file-pdf-fill me-1"></i> PDF
                </a>

                <a href="{{ route('purchase.index') }}"
                    class="mdc-button mdc-button--unelevated filled-button--secondary mdc-ripple-upgraded">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm">
            <div class="card-body px-4 py-4">

                <div class="row align-items-center mb-4">

                    {{-- LEFT: LOGO --}}
                    <div class="col-6 d-flex align-items-center">

                        @if (isset($company->company_logo) && file_exists(public_path($company->company_logo)))
                            <img src="{{ asset($company->company_logo) }}" alt="Logo" style="max-height: 90px;">
                        @else
                            {{-- Same style as screenshot --}}
                            <div class="text-center" style="color:#888;">
                                <i class="bi bi-image fs-1"></i>
                                <div class="small">NO IMAGE<br>FOUND</div>
                            </div>
                        @endif

                    </div>

                    {{-- RIGHT: SHOP NAME & ADDRESS --}}
                    <div class="col-6 text-end">
                        <h2 class="fw-bold" style="color:#007bff;">
                            {{ $company->name ?? 'Shop Name' }}
                        </h2>

                        <p class="text-muted fw-semibold mb-0">
                            {{ $company->city?->name ?? '' }}, {{ $company->state?->name ?? '' }}
                        </p>
                    </div>

                </div>

                <hr style="border-top: 2px solid #3e2f66;">

                <!-- TITLE -->
                <div class="mb-4 d-flex justify-content-between">
                    <h4 class="fw-semibold">Purchase Invoice</h4>
                    <span class="text-muted">Date: {{ formatDate($purchase->purchase_date) }} </span>
                </div>

                <!-- SUPPLIER + BILL INFO -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-uppercase small mb-1">Bill From:</h6>

                        <h5 class="fw-bold mb-1">
                            {{ $purchase->supplier->first_name . ' ' . $purchase->supplier->last_name }}
                        </h5>


                        <p class="text-muted mb-0">
                            {{ $purchase->supplier->billing_address ?? '-' }}
                        </p>

                        @if ($purchase->supplier->city || $purchase->supplier->state || $purchase->supplier->pincode)
                            <p class="text-muted mb-0">
                                {{ $purchase->supplier->cityData?->name ?? '' }},
                                {{ $purchase->supplier->stateData?->name ?? '' }}
                                {{ $purchase->supplier->pincode ? ' - ' . $purchase->supplier->pincode : '' }}
                            </p>
                        @endif

                        @if ($purchase->supplier->email)
                            <p class="text-muted mb-0">
                                {{ $purchase->supplier->email ?? '-' }}
                            </p>
                        @endif

                        @if ($purchase->supplier->mobile_number)
                            <p class="text-muted mb-0">
                                {{ $purchase->supplier->mobile_number }}
                            </p>
                        @endif
                    </div>


                    <div class="col-md-6">
                        <h6 class="fw-bold">SHIP FROM:</h6>
                        <p class="mb-1"><strong>Bill No:</strong> {{ $purchase->bill_no }}</p>
                        <p class="mb-1"><strong>Purchase Code:</strong> {{ $purchase->reference_no ?? '-' }}</p>
                        <p class="mb-1"><strong>Status:</strong>
                            @if ($purchase->payment_status == 'PAID')
                                <span class="badge bg-success">PAID</span>
                            @elseif($purchase->payment_status == 'PARTIAL')
                                <span class="badge bg-warning text-dark">PARTIAL</span>
                            @else
                                <span class="badge bg-danger">DUE</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- ITEMS TABLE -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th class="text-end">Unit Cost</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Disc</th>
                                <th class="text-end">GST %</th>
                                <th class="text-end">GST Amt</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($purchase->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->product->name }}</td>
                                    <td class="text-end">{{ moneyFormat($item->unit_cost, 2) }}</td>
                                    <td class="text-end">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ $item->discount ?? 0 }}</td>
                                    <td class="text-end">{{ $item->gst_percent }}</td>
                                    <td class="text-end">{{ moneyFormat($item->gst_amount, 2) }}</td>
                                    <td class="text-end">{{ moneyFormat($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- TOTALS -->
                <div class="row justify-content-end">
                    <div class="col-md-4">

                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">Subtotal:</span>
                            <span>{{ moneyFormat($purchase->subtotal, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">Total GST:</span>
                            <span>{{ moneyFormat($purchase->tax_amount, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">Shipping Charges:</span>
                            <span>{{ moneyFormat($purchase->shipping_charges, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">Rounding:</span>
                            <span>{{ moneyFormat($purchase->rounding, 2) }}</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold fs-5">Grand Total:</span>
                            <span class="fw-bold fs-5">{{ moneyFormat($purchase->total_amount, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">Paid:</span>
                            <span>{{ moneyFormat($purchase->paid_amount, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold text-danger">Due:</span>
                            <span class="text-danger">{{ moneyFormat($purchase->due_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- NOTES -->
                @if ($purchase->notes)
                    <div class="mb-4">
                        <h6 class="fw-bold">Notes</h6>
                        <p>{{ $purchase->notes }}</p>
                    </div>
                @endif

                <!-- ATTACHMENT -->
                @if ($purchase->attachment)
                    <div>
                        <h6 class="fw-bold">Attachment</h6>
                        <a href="{{ asset($purchase->attachment) }}" target="_blank"
                            class="btn btn-outline-success btn-sm">
                            View Attachment
                        </a>
                    </div>
                @endif

            </div>
        </div>

    </div>
@endsection


@push('footer-script')
    <script src="{{ asset('assets/backend/js/purchase.js') }}"></script>
@endpush
