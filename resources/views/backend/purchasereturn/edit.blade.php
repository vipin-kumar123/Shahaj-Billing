@extends('backend.layouts.app')
@section('title')
    Billing Software | Edit Purchase Return
@endsection

@section('content')
    <div class="container-fluid px-2">

        <!-- Breadcrumb -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Purchase</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>Purchase Return Bills</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span class="fw-semibold text-dark">Edit </span>
            </div>

            <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="{{ route('purchase.return.index') }}" class="mdc-button mdc-button--danger btn-sm">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>


        <!-- PURCHASE HEADER -->
        <div class="card mb-3 shadow-sm">
            <div class="card-body">

                <h6 class="fw-semibold mb-3">Purchase Details</h6>

                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label">Supplier</label>
                        <input type="text" class="form-control"
                            value="{{ $purchase->supplier->first_name }} {{ $purchase->supplier->last_name }}" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Bill No</label>
                        <input type="text" class="form-control" value="{{ $purchase->bill_no }}" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Purchase Date</label>
                        <input type="text" class="form-control" value="{{ $purchase->purchase_date }}" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Total Amount</label>
                        <input type="text" class="form-control" value="₹{{ $purchase->total_amount }}" readonly>
                    </div>

                </div>

            </div>
        </div>

        <form action="{{ route('purchase.return.update', $purchaseReturn->id) }}" method="POST">
            @csrf

            <!-- RETURN DETAILS -->
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <div class="row g-3 align-items-start">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Return Date</label>
                            <input type="text" name="return_date" class="form-control datepicker"
                                value="{{ $purchaseReturn->return_date }}" required>
                        </div>
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
                                    <th>Purchased Qty</th>
                                    <th>Return Qty</th>
                                    <th>Unit Cost</th>
                                    <th>GST %</th>
                                    <th>Total (Auto)</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($items as $item)
                                    <tr>

                                        <!-- PRODUCT NAME -->
                                        <td>{{ $item->product->name }}</td>

                                        <!-- PURCHASED QTY -->
                                        <td>{{ $item->purchased_qty }}</td>

                                        <!-- RETURN QTY (EDIT + SHOW Already Returned) -->
                                        <td>
                                            <div class="small text-danger mb-1">
                                                Already Returned:
                                                <strong>{{ $item->return_qty }}</strong>
                                            </div>

                                            <input type="number" name="return_qty[{{ $item->purchase_item_id }}]"
                                                class="form-control return-qty" value="{{ $item->return_qty }}"
                                                min="0" max="{{ $item->purchased_qty }}"
                                                data-cost="{{ $item->unit_cost }}" data-gst="{{ $item->gst_percent }}"
                                                oninput="calculateRow(this)">
                                        </td>

                                        <!-- UNIT COST -->
                                        <td>₹{{ $item->unit_cost }}</td>

                                        <!-- GST -->
                                        <td>{{ $item->gst_percent }}%</td>

                                        <!-- TOTAL (AUTO) -->
                                        <td>
                                            <input type="text" class="form-control line-total"
                                                value="{{ $item->total }}" {{-- IMPORTANT --}} readonly>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-end">Total Return Amount:</th>
                                    <th>
                                        <input type="text" id="total_return_amount"
                                            value="{{ $purchaseReturn->total_return_amount }}" class="form-control fw-bold"
                                            readonly>
                                    </th>
                                </tr>
                            </tfoot>

                        </table>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Note</label>
                        <textarea name="note" class="form-control" rows="2">{{ $purchaseReturn->note }}</textarea>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="mdc-button mdc-button--unelevated px-5">
                            <i class="bi bi-check-circle me-2"></i> Update Return
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
    <script src="{{ asset('assets/backend/js/purchasereturn.js') }}"></script>
@endpush
