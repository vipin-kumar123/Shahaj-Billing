@extends('backend.layouts.app')
@section('title')
    Billing Software | Sale Details
@endsection


@section('content')
    <div class="container-fluid px-2">

        <!-- BREADCRUMB -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Sale</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>Sale Invoice</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span class="fw-semibold text-dark">Sale</span>
            </div>

            <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="{{ route('sale.return.convert', $sale->id) }}"
                    class="mdc-button mdc-button--outlined outlined-button--success mdc-ripple-upgraded">
                    <i class="bi bi-arrow-left-right me-1"></i> Convert to Return
                </a>
                <a href="{{ route('sale.edit', $sale->id) }}"
                    class="mdc-button mdc-button--outlined outlined-button--primary mdc-ripple-upgraded">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
                <button onclick="window.open('{{ route('sale.print', $sale->id) }}', '_blank')"
                    class="mdc-button mdc-button--outlined outlined-button--dark">
                    <i class="bi bi-printer me-1"></i> Print
                </button>

                <a href="{{ route('sale.pdf', $sale->id) }}"
                    class="mdc-button mdc-button--outlined outlined-button--secondary mdc-ripple-upgraded">
                    <i class="bi bi-file-pdf-fill me-1"></i> PDF
                </a>

                <a href="{{ route('sale.index') }}"
                    class="mdc-button mdc-button--unelevated filled-button--secondary mdc-ripple-upgraded">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body px-4 py-4">

                <!-- HEADER -->
                <div class="row align-items-center mb-4">

                    <!-- LOGO -->
                    <div class="col-6">
                        @if (isset($company->company_logo) && file_exists(public_path($company->company_logo)))
                            <img src="{{ asset($company->company_logo) }}" style="max-height: 90px;">
                        @endif
                    </div>

                    <!-- COMPANY INFO -->
                    <div class="col-6 text-end">
                        <h2 class="fw-bold text-primary">
                            {{ $company->name ?? 'Shop Name' }}
                        </h2>
                        <p class="text-muted mb-0">
                            {{ $company->city?->name ?? '' }},
                            {{ $company->state?->name ?? '' }}
                        </p>
                    </div>
                </div>

                <hr>

                <!-- TITLE -->
                <div class="mb-4 d-flex justify-content-between">
                    <h4 class="fw-semibold">Sale Invoice</h4>
                    <span class="text-muted">
                        <p class="text-muted mb-0"><strong>Date:</strong> {{ formatDate($sale->sale_date) }}</p>
                    </span>
                </div>

                <!-- CUSTOMER INFO -->
                <div class="row mb-4 align-items-start">

                    <!-- BILL TO (LEFT) -->
                    <div class="col-md-4 text-start">
                        <h6 class="fw-bold text-uppercase small mb-1">Bill To:</h6>

                        <h5 class="fw-bold mb-1">
                            {{ $sale->customer->first_name . ' ' . $sale->customer->last_name }}
                        </h5>

                        <p class="text-muted mb-0">
                            {{ $sale->customer->billing_address ?? '-' }}
                        </p>

                        <p class="text-muted mb-0">
                            {{ $sale->customer->cityData?->name ?? '' }},
                            {{ $sale->customer->stateData?->name ?? '' }}
                            {{ $sale->customer->pincode ? ' - ' . $sale->customer->pincode : '' }}
                        </p>

                        <p class="text-muted mb-0">
                            {{ $sale->customer->mobile_number ?? '' }}
                        </p>
                    </div>

                    <!-- SHIP TO (CENTER) -->
                    <div class="col-md-4 offset-md-0 d-flex justify-content-center">
                        <div class="text-start" style="max-width: 320px;">

                            <h6 class="fw-bold text-uppercase small mb-1">SHIP TO:</h6>

                            <p class="text-muted mb-0">
                                {{ $sale->customer->shipping_address ?? $sale->customer->billing_address }}
                            </p>

                            <p class="text-muted mb-0">
                                {{ $sale->customer->cityData?->name ?? '' }},
                                {{ $sale->customer->stateData?->name ?? '' }}
                            </p>

                        </div>
                    </div>



                    <!-- BILL INFO (RIGHT) -->
                    <div class="col-md-4 text-end">
                        {{-- <p class="mb-1"><strong>Bill No:</strong> {{ $sale->invoice_no }}</p> --}}
                        <h6 class="fw-bold text-primary">#{{ $sale->reference_no ?? '-' }}</h6>

                        <p class="mb-1">
                            <strong>Status:</strong>
                            @if ($sale->payment_status == 'PAID')
                                <span class="badge bg-success">PAID</span>
                            @elseif($sale->payment_status == 'PARTIAL')
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
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Disc</th>
                                <th class="text-end">GST %</th>
                                <th class="text-end">GST Amt</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($sale->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->product->name }}</td>
                                    <td class="text-end">{{ moneyFormat($item->price, 2) }}</td>
                                    <td class="text-end">{{ rtrim(rtrim($item->quantity, '0'), '.') }}</td>
                                    <td class="text-end">{{ $item->discount ?? 0 }}</td>
                                    <td class="text-end">{{ $item->tax_percent }}</td>
                                    <td class="text-end">{{ moneyFormat($item->tax_amount, 2) }}</td>
                                    <td class="text-end">{{ moneyFormat($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- TOTALS -->
                <div class="row justify-content-end">
                    <div class="col-md-4">

                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span>{{ moneyFormat($sale->subtotal, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span>Total GST:</span>
                            <span>{{ moneyFormat($sale->tax_amount, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span>Shipping:</span>
                            <span>{{ moneyFormat($sale->shipping_charges, 2) }}</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Grand Total:</span>
                            <span>{{ moneyFormat($sale->total_amount, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span>Paid:</span>
                            <span>{{ moneyFormat($sale->paid_amount, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between text-danger">
                            <span>Due:</span>
                            <span>{{ moneyFormat($sale->due_amount, 2) }}</span>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection



@push('footer-script')
    <script src="{{ asset('assets/backend/js/sale.js') }}"></script>
@endpush
