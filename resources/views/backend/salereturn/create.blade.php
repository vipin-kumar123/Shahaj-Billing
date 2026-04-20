@extends('backend.layouts.app')
@section('title')
    Billing Software | Sale Return
@endsection

@section('content')
    <div class="container-fluid px-2">

        <!-- Breadcrumb -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Sale</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>Sale Return</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span class="fw-semibold text-dark">Create </span>
            </div>

            <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="{{ route('sale.return.index') }}" class="mdc-button mdc-button--danger btn-sm">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <!-- PURCHASE HEADER -->
        <div class="card mb-3 shadow-sm">
            <div class="card-body">

                <h6 class="fw-semibold mb-3">Sale Return Details</h6>

                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label">Customer</label>
                        <input type="text" class="form-control"
                            value="{{ $sale->customer->first_name }} {{ $sale->customer->last_name }}" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Invoice No</label>
                        <input type="text" class="form-control" value="{{ $sale->invoice_no }}" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Sale Date</label>
                        <input type="text" class="form-control" value="{{ formatDate($sale->sale_date) }}" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Total Amount</label>
                        <input type="text" class="form-control" value="₹{{ $sale->total_amount }}" readonly>
                    </div>

                </div>

            </div>
        </div>

        <form id="saleReturnForm" method="POST">
            @csrf
            <!-- RETURN DETAILS -->
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <div class="row g-3 align-items-start">

                        <!-- LEFT SIDE : Return Date -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Return Date <span class="text-danger">*</span></label>
                            <input type="text" name="return_date" class="form-control datepicker" placeholder="YY/mm/dd"
                                required>
                        </div>

                        <!-- RIGHT SIDE : Compact Info Box -->
                        <div class="col-md-8">
                            <div class="flex-grow-1 p-3 mt-4 rounded" style="background:#f4f6ff; border:1px solid #d9e1ff;">

                                <div class="d-flex align-items-center" style="font-size:14px;">

                                    <i class="bi bi-info-circle text-primary me-2"></i>

                                    <span>
                                        Returned items will update <strong>stock</strong>,
                                        adjust <strong>supplier balance</strong>, and create a
                                        <strong>ledger entry</strong>.
                                    </span>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>
            </div>


            <!-- RETURN ITEMS -->
            <div class="card shadow-sm">
                <div class="card-body">

                    <h6 class="fw-semibold mb-3">Return Items</h6>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Sale Qty</th>
                                    <th>Return Qty</th>
                                    <th>Unit Cost</th>
                                    <th>GST %</th>
                                    <th>Total (Auto)</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($sale->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>

                                        <td>{{ rtrim(rtrim($item->quantity, '0'), '.') }}</td>

                                        <td>
                                            <input type="number" name="return_qty[{{ $item->id }}]"
                                                class="form-control return-qty" data-cost="{{ $item->price }}"
                                                data-gst="{{ $item->tax_percent }}" max="{{ $item->quantity }}"
                                                min="0" step="0.01" oninput="calculateRow(this)">
                                        </td>

                                        <td>₹{{ $item->price }}</td>
                                        <td>{{ $item->tax_percent }}%</td>

                                        <td>
                                            <input type="text" class="form-control line-total" readonly>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-end">Total Return Amount:</th>
                                    <th>
                                        <input type="text" id="total_return_amount" class="form-control fw-bold"
                                            readonly>
                                    </th>
                                </tr>
                            </tfoot>

                        </table>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Note</label>
                        <textarea name="note" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="mt-4">
                        <button type="submit" id="returnSave" class="mdc-button mdc-button--unelevated px-5">
                            <i class="bi bi-check-circle me-2"></i> Save Return
                        </button>
                    </div>

                </div>
            </div>
        </form>

    </div>
    </div>

    </div>
@endsection



@push('footer-script')
    <script>
        window.SALE_RETURN_STORE_ROUTE = "{{ route('sale.return.store', $sale->id) }}";

        window.SALE_RETURN_INDEX_ROUTE = "{{ route('sale.return.index') }}";
    </script>
    <script src="{{ asset('assets/backend/js/sale.js') }}"></script>
@endpush
