@extends('backend.layouts.app')
@section('title')
    Billing Software | Sale return invoice
@endsection

@section('content')
    <div class="container-fluid px-2">
        <!-- BREADCRUMB -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Sale</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>Sale Return</span>

            </div>

            <div class="d-flex gap-2 mt-2 mt-md-0">

                <a href="{{ route('sale.return.edit', $saleReturn->id) }}"
                    class="mdc-button mdc-button--outlined outlined-button--primary mdc-ripple-upgraded">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
                <button onclick="window.open('{{ route('sale.return.print', $saleReturn->id) }}', '_blank')"
                    class="mdc-button mdc-button--outlined outlined-button--dark">
                    <i class="bi bi-printer me-1"></i> Print
                </button>

                <a href="{{ route('sale.return.pdf', $saleReturn->id) }}"
                    class="mdc-button mdc-button--outlined outlined-button--secondary mdc-ripple-upgraded">
                    <i class="bi bi-file-pdf-fill me-1"></i> PDF
                </a>

                <a href="{{ route('sale.return.index') }}"
                    class="mdc-button mdc-button--unelevated filled-button--secondary mdc-ripple-upgraded">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">

                <!-- ============================= -->
                <!-- COMPANY / HEADER SECTION -->
                <!-- ============================= -->
                <div class="d-flex justify-content-between mb-4">

                    <!-- COMPANY LOGO -->
                    <div style="max-width: 45%;">
                        @if (!empty($company->company_logo) && file_exists(public_path($company->company_logo)))
                            <img src="{{ asset($company->company_logo) }}" style="max-height: 90px;" class="mb-2">
                        @endif
                    </div>

                    <!-- COMPANY DETAILS -->
                    <div class="text-end" style="max-width: 45%;">
                        <h4 class="fw-bold text-primary m-0">{{ $company->name }}</h4>
                        <p class="text-muted mb-1">{{ $company->address }}</p>
                        <p class="text-muted mb-0">
                            {{ $company->city?->name ?? '' }},
                            {{ $company->state?->name ?? '' }}
                        </p>
                    </div>
                </div>

                <hr>

                <!-- ============================= -->
                <!-- CUSTOMER + RETURN INFO -->
                <!-- ============================= -->
                <div class="d-flex justify-content-between mb-4">

                    <!-- CUSTOMER DETAILS -->
                    <div style="max-width: 45%;">
                        <h6 class="fw-semibold mb-2">Return From (Customer):</h6>

                        <h5 class="fw-bold m-0">
                            {{ $saleReturn->customer->first_name }}
                            {{ $saleReturn->customer->last_name }}
                        </h5>

                        <p class="mb-1">Mobile: {{ $saleReturn->customer->mobile_number }}</p>
                        <p class="mb-1">{{ $saleReturn->customer->billing_address }}</p>
                        <p class="mb-0">GSTIN: {{ $saleReturn->customer->gst_number ?? '-' }}</p>
                    </div>

                    <!-- RETURN INFO -->
                    <div class="text-end" style="max-width: 45%;">
                        <p class="mb-1"><strong>Return No:</strong> {{ $saleReturn->return_no }}</p>
                        <p class="mb-1"><strong>Date:</strong> {{ formatDate($saleReturn->return_date) }}</p>
                        <p class="mb-0"><strong>Against Invoice:</strong> {{ $saleReturn->sale->invoice_no }}</p>
                    </div>

                </div>

                <!-- ============================= -->
                <!-- ITEMS TABLE -->
                <!-- ============================= -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th class="text-end">Returned Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">GST %</th>
                                <th class="text-end">GST Amount</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php $totalGst = 0; @endphp
                            @foreach ($saleReturn->items as $key => $item)
                                @php $totalGst += $item->gst_amount; @endphp

                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->product->name }}</td>

                                    <!-- Qty: remove trailing zeros -->
                                    <td class="text-end">
                                        {{ rtrim(rtrim($item->return_qty, '0'), '.') }}
                                    </td>

                                    <td class="text-end">₹{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end">{{ $item->gst_percent }}%</td>
                                    <td class="text-end">₹{{ number_format($item->gst_amount, 2) }}</td>
                                    <td class="text-end">₹{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- ============================= -->
                <!-- TOTAL SECTION -->
                <!-- ============================= -->
                <div class="row justify-content-end mt-3">
                    <div class="col-md-4">

                        <table class="table table-sm table-borderless">
                            <tr>
                                <th>Total GST:</th>
                                <td class="text-end">₹{{ number_format($totalGst, 2) }}</td>
                            </tr>

                            <tr class="border-top fw-bold">
                                <th>Total Return Amount:</th>
                                <td class="text-end text-danger">
                                    ₹{{ number_format($saleReturn->total_return_amount, 2) }}
                                </td>
                            </tr>

                            <tr>
                                <th>Refunded:</th>
                                <td class="text-end">
                                    ₹{{ number_format($saleReturn->refunded_amount ?? 0, 2) }}
                                </td>
                            </tr>

                            <tr class="fw-bold text-primary">
                                <th>Refund Due:</th>
                                <td class="text-end">₹{{ number_format($saleReturn->refund_due ?? 0, 2) }}</td>
                            </tr>
                        </table>

                    </div>
                </div>

                <!-- ============================= -->
                <!-- NOTE -->
                <!-- ============================= -->
                @if ($saleReturn->note)
                    <div class="mt-4">
                        <strong>Note:</strong>
                        <p>{{ $saleReturn->note }}</p>
                    </div>
                @endif

                <hr>

                <!-- ============================= -->
                <!-- FOOTER SIGNATURE -->
                <!-- ============================= -->
                <div class="d-flex justify-content-between mt-5">
                    <p class="mb-0"><strong>Prepared By:</strong> {{ auth()->user()->name }}</p>

                    <p class="mb-0 text-end">Authorized Signatory</p>
                </div>

            </div>
        </div>

    </div>
@endsection
