@extends('backend.layouts.app')
@section('title')
    Billing Software | Edit Sale
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
                <span class="fw-semibold text-dark">Edit Sale</span>
            </div>

            <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="{{ route('sale.index') }}" class="mdc-button mdc-button--danger btn-sm">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body px-4 py-4">

                <form id="saleUpdateForm" method="POST" enctype="multipart/form-data">

                    @csrf

                    <!-- SALE HEADER -->
                    <h5 class="mb-3 fw-semibold">Sale Details</h5>

                    <div class="row g-3">

                        <div class="col-md-3">
                            <label class="form-label">Customer <span class="text-danger">*</span></label>
                            <select name="customer_id" class="form-select select2">
                                <option value="">Select Customer</option>
                                @foreach ($customers as $cus)
                                    <option value="{{ $cus->id }}"
                                        {{ $sale->customer_id == $cus->id ? 'selected' : '' }}>
                                        {{ $cus->first_name }} {{ $cus->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Invoice Date <span class="text-danger">*</span></label>
                            <input type="date" name="sale_date" class="form-control"
                                value="{{ old('sale_date', \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d')) }}"
                                required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Invoice No</label>
                            <input type="text" name="invoice_no" class="form-control"
                                value="{{ old('invoice_no', $sale->invoice_no) }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Sale Code</label>
                            <input type="text" class="form-control" value="{{ $sale->reference_no }}" disabled>
                        </div>

                    </div>

                    <hr class="my-4">

                    <!-- SALE ITEMS -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-semibold">Sale Items</h5>
                        <button type="button" class="btn btn-dark btn-sm" onclick="addRow()">+ Add Item</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 20%">Product</th>
                                    <th style="width: 10%">Unit Price</th>
                                    <th style="width: 10%">Qty</th>
                                    <th style="width: 8%">Discount</th>
                                    <th style="width: 8%">Type</th>
                                    <th style="width: 8%">GST %</th>
                                    <th style="width: 10%">GST Amt</th>
                                    <th style="width: 12%">Total</th>
                                    <th style="width: 5%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sale->items as $item)
                                    <tr>
                                        <td>
                                            <select name="product_id[]" class="form-select product-select select2">
                                                @foreach ($products as $p)
                                                    <option value="{{ $p->id }}" data-price="{{ $p->saleing_price }}"
                                                        data-gst="{{ $p->gst_percentage }}"
                                                        {{ $item->product_id == $p->id ? 'selected' : '' }}>
                                                        {{ $p->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td>
                                            <input type="number" step="0.01" name="unit_price[]"
                                                class="form-control unit-cost" value="{{ $item->price }}">
                                        </td>

                                        <td>
                                            <input type="number" step="0.001" name="quantity[]"
                                                class="form-control quantity" value="{{ $item->quantity }}">
                                        </td>

                                        <td>
                                            <input type="number" name="discount[]" class="form-control discount"
                                                value="{{ $item->discount }}">
                                        </td>

                                        <td>
                                            <select name="discount_type[]" class="form-select discount-type">
                                                <option value="flat"
                                                    {{ $item->discount_type == 'flat' ? 'selected' : '' }}>Flat</option>
                                                <option value="percent"
                                                    {{ $item->discount_type == 'percent' ? 'selected' : '' }}>%</option>
                                            </select>
                                        </td>

                                        <td>
                                            <input type="number" name="gst_percent[]" class="form-control gst-percent"
                                                value="{{ $item->tax_percent }}">
                                        </td>

                                        <td>
                                            <input type="text" name="gst_amount[]" class="form-control gst-amount"
                                                value="{{ $item->tax_amount }}" readonly>
                                        </td>

                                        <td>
                                            <input type="text" name="total[]" class="form-control line-total"
                                                value="{{ $item->subtotal }}" readonly>
                                        </td>

                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-4">

                    <!-- TOTAL SECTION -->
                    <div class="row g-3 justify-content-end">
                        <div class="col-md-4">

                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <input type="text" name="subtotal" id="subtotal"
                                    class="form-control form-control-sm w-50" value="{{ $sale->subtotal }}" readonly>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Total GST:</span>
                                <input type="text" name="tax_amount" id="tax_amount" value="{{ $sale->tax_amount }}"
                                    class="form-control form-control-sm w-50" readonly>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <input type="number" name="shipping_charges" id="shipping_charges"
                                    value="{{ $sale->shipping_charges }}" class="form-control form-control-sm w-50"
                                    value="0" oninput="calculateTotal()">
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold">Grand Total:</span>
                                <input type="text" name="total_amount" id="total_amount"
                                    value="{{ $sale->total_amount }}" class="form-control form-control-sm w-50 fw-bold"
                                    readonly>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Paid:</span>
                                <input type="number" name="paid_amount" id="paid_amount"
                                    value="{{ $sale->paid_amount }}" class="form-control form-control-sm w-50"
                                    value="0" oninput="calculateDue()">
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Due:</span>
                                <input type="text" name="due_amount" id="due_amount" value="{{ $sale->due_amount }}"
                                    class="form-control form-control-sm w-50" readonly>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Payment Method:</span>
                                <select name="payment_method" class="form-select form-select-sm w-50">
                                    <option value="">Select</option>
                                    <option value="Cash" {{ $sale->payment_method == 'Cash' ? 'selected' : '' }}>
                                        Cash
                                    </option>
                                    <option value="UPI" {{ $sale->payment_method == 'UPI' ? 'selected' : '' }}>
                                        UPI</option>
                                    <option value="Bank" {{ $sale->payment_method == 'Bank' ? 'selected' : '' }}>
                                        Bank</option>
                                    <option value="Card" {{ $sale->payment_method == 'Card' ? 'selected' : '' }}>
                                        Card</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <hr class="my-4">

                    <button type="submit" id="editSaleBtn" class="mdc-button mdc-button--unelevated px-5">
                        <i class="bi bi-check-circle me-2"></i> Save Sale
                    </button>

                </form>
            </div>
        </div>
    </div>


    <!-- TEMPLATE ROW -->
    <template id="itemRowTemplate">
        <tr>
            <td>
                <select name="product_id[]" class="form-select product-select select2" onchange="productSelected(this)"
                    required>
                    <option value="">Select</option>
                    @foreach ($products as $p)
                        <option value="{{ $p->id }}" data-price="{{ $p->saleing_price }}"
                            data-gst="{{ $p->gst_percentage }}">
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </td>

            <td><input type="number" step="0.01" name="unit_price[]" class="form-control unit-cost"
                    oninput="updateRow(this)" required></td>

            <td><input type="number" step="0.001" name="quantity[]" class="form-control quantity"
                    oninput="updateRow(this)"></td>

            <td><input type="number" name="discount[]" class="form-control discount" oninput="updateRow(this)"></td>

            <td>
                <select name="discount_type[]" class="form-select discount-type" onchange="updateRow(this)">
                    <option value="flat">Flat</option>
                    <option value="percent">%</option>
                </select>
            </td>

            <td><input type="number" name="gst_percent[]" class="form-control gst-percent" oninput="updateRow(this)">
            </td>

            <td><input type="text" name="gst_amount[]" class="form-control gst-amount" readonly></td>

            <td><input type="text" name="total[]" class="form-control line-total" readonly></td>

            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)">
                    <i class="bi bi-x"></i>
                </button>
            </td>
        </tr>
    </template>
@endsection


@push('footer-script')
    <script>
        window.PURCHASE_UPDATE_ROUTE = "{{ route('sale.update', $sale->id) }}";
    </script>
    <script src="{{ asset('assets/backend/js/sale.js') }}"></script>
@endpush
