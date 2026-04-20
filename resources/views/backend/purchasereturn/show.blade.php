@extends('backend.layouts.app')
@section('title')
    Billing Software | Purchase return invoice
@endsection

@section('content')
    <div class="container-fluid px-2">

        <!-- BREADCRUMB -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Purchase</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>Purchase Return</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span class="fw-semibold text-dark">Invoice </span>
            </div>

            <div class="d-flex gap-2 mt-2 mt-md-0">

                <a href="{{ route('purchase.return.edit', $purchaseReturn->id) }}"
                    class="mdc-button mdc-button--outlined outlined-button--primary mdc-ripple-upgraded">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
                <button onclick="window.open('{{ route('purchase.return.print', $purchaseReturn->id) }}', '_blank')"
                    class="mdc-button mdc-button--outlined outlined-button--dark">
                    <i class="bi bi-printer me-1"></i> Print
                </button>

                <a href="{{ route('purchase.return.pdf', $purchaseReturn->id) }}"
                    class="mdc-button mdc-button--outlined outlined-button--secondary mdc-ripple-upgraded">
                    <i class="bi bi-file-pdf-fill me-1"></i> PDF
                </a>

                <a href="{{ route('purchase.return.index') }}"
                    class="mdc-button mdc-button--unelevated filled-button--secondary mdc-ripple-upgraded">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">

                <!-- HEADER -->
                <div class="d-flex justify-content-between mb-4">

                    <!-- COMPANY -->
                    <div style="max-width: 45%;">
                        @if (isset($company->company_logo) && file_exists(public_path($company->company_logo)))
                            <img class="mb-2" src="{{ asset($company->company_logo) }}" style="max-height: 90px;">
                        @endif
                    </div>

                    <!-- COMPANY INFO -->
                    <div class="text-end" style="max-width: 25%;">
                        <h5 class="fw-bold text-primary">{{ $company->name }}</h5>
                        <p class="text-muted mb-0">
                            {{ $company->city?->name ?? '' }},
                            {{ $company->state?->name ?? '' }}<br>
                            {{ $company->address ?? '' }}
                        </p>
                    </div>
                </div>

                <hr>

                <!-- SUPPLIER INFO -->
                <div class="d-flex justify-content-between mb-4">
                    <div style="max-width: 45%;">
                        <h6 class="fw-semibold">Return To (Supplier):</h6>
                        <h5 class="fw-bold">
                            {{ $purchaseReturn->supplier->first_name }} {{ $purchaseReturn->supplier->last_name }}
                        </h5>

                        <p class="mb-1">{{ $purchaseReturn->supplier->mobile_number }}</p>
                        <p class="mb-1">{{ $purchaseReturn->supplier->billing_address }}</p>
                        <p class="mb-0">GST: {{ $purchaseReturn->supplier->gst_number ?? '-' }}</p>
                    </div>

                    <div class="text-end" style="max-width: 45%;">
                        <p class="mb-0"><strong>Return No:</strong> {{ $purchaseReturn->return_no }}</p>
                        <p class="mb-0"><strong>Date:</strong> {{ $purchaseReturn->return_date }}</p>
                        <p class="mb-0"><strong>Against Bill:</strong> {{ $purchaseReturn->purchase->bill_no }}</p>
                    </div>
                </div>

                <!-- ITEMS TABLE -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Return Qty</th>
                                <th>Unit Cost</th>
                                <th>GST %</th>
                                <th>GST Amount</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalGst = 0; @endphp
                            @foreach ($purchaseReturn->items as $key => $item)
                                @php $totalGst += $item->gst_amount; @endphp
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->return_qty ?? 0 }}</td>
                                    <td>₹{{ number_format($item->unit_cost, 2) }}</td>
                                    <td>{{ $item->gst_percent }}%</td>
                                    <td>₹{{ number_format($item->gst_amount, 2) }}</td>
                                    <td>₹{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- TOTAL SECTION -->
                <div class="row justify-content-end mt-3">
                    <div class="col-md-4">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th>Total GST:</th>
                                <td class="text-end">₹{{ number_format($totalGst, 2) }}</td>
                            </tr>
                            <tr class="fw-bold border-top">
                                <th>Total Return Amount:</th>
                                <td class="text-end text-danger">
                                    ₹{{ number_format($purchaseReturn->total_return_amount, 2) }}
                                </td>
                            </tr>

                            <!-- ADD REFUND SUMMARY -->
                            <tr>
                                <th>Already Refunded:</th>
                                <td class="text-end text-success">
                                    ₹{{ number_format($purchaseReturn->refunded_amount, 2) }}
                                </td>
                            </tr>

                            <tr class="fw-bold">
                                <th>Refund Due:</th>
                                <td class="text-end text-primary">
                                    ₹{{ number_format($purchaseReturn->refund_due, 2) }}
                                </td>
                            </tr>

                            <tr>
                                <th>Status:</th>
                                <td class="text-end">
                                    <span
                                        class="badge bg-{{ $purchaseReturn->refund_status == 'REFUNDED' ? 'success' : ($purchaseReturn->refund_status == 'PARTIAL' ? 'warning' : 'danger') }}">
                                        {{ $purchaseReturn->refund_status }}
                                    </span>
                                </td>
                            </tr>

                        </table>
                    </div>
                </div>

                <!-- NOTE -->
                @if ($purchaseReturn->note)
                    <div class="mt-4">
                        <strong>Note:</strong>
                        <p>{{ $purchaseReturn->note }}</p>
                    </div>
                @endif

                <hr>

                <!-- FOOTER -->
                <div class="d-flex justify-content-between mt-5">
                    <div>
                        <p class="mb-0"><strong>Prepared By:</strong> {{ auth()->user()->name }}</p>
                    </div>
                    <div class="text-end">
                        <p class="mb-0">Authorized Signatory</p>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
