@extends('backend.layouts.app')
@section('title')
    Billing Software | Edit Purchase
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
                <span class="fw-semibold text-dark">Update Purchase</span>
            </div>

            <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="{{ route('purchase.index') }}" class="mdc-button mdc-button--danger btn-sm">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm">
            <div class="card-body px-4 py-4">

                <form id="purchaseUpdateForm" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- PURCHASE HEADER -->
                    <h5 class="mb-3 fw-semibold">Purchase Details</h5>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select name="supplier_id" class="form-select select2">
                                <option value="">Select Supplier</option>
                                @isset($suppliers)
                                    @foreach ($suppliers as $sup)
                                        <option value="{{ $sup->id }}"
                                            {{ old('supplier_id', $purchase->supplier_id == $sup->id) ? 'selected' : '' }}>
                                            {{ $sup->first_name, ' ' . $sup->last_name }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Bill Date<span class="text-danger">*</span></label>
                            <input type="date" name="purchase_date" class="form-control datepicker"
                                value="{{ $purchase->purchase_date ?? now()->format('Y-m-d') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Bill No<span class="text-danger">*</span></label>
                            <input type="text" name="bill_no" class="form-control" value="{{ $purchase->bill_no }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Purchase Code<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <!-- Auto Number -->
                                <input type="text" class="form-control" value="{{ $purchase->reference_no }}"
                                    placeholder="6981C3B375EAD" disabled="disabled">
                            </div>
                        </div>


                    </div>

                    <hr class="my-4">

                    <!-- ITEMS SECTION -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-semibold">Purchase Items</h5>
                        <button type="button" class="btn btn-dark btn-sm" onclick="addRow()">+ Add Item</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 20%">Product</th>
                                    <th style="width: 10%">Unit Cost</th>
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
                                @foreach ($purchase->items as $item)
                                    <tr>
                                        <td>
                                            <select name="product_id[]" class="form-select select2 product-select"
                                                onchange="productSelected(this)" required>
                                                <option value="">Select</option>
                                                @foreach ($products as $p)
                                                    <option value="{{ $p->id }}"
                                                        data-price="{{ $p->purchase_price }}"
                                                        data-gst="{{ $p->gst_percentage }}"
                                                        {{ $item->product_id == $p->id ? 'selected' : '' }}>
                                                        {{ $p->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td>
                                            <input type="number" step="0.01" name="unit_cost[]"
                                                class="form-control unit-cost" value="{{ $item->unit_cost }}"
                                                oninput="updateRow(this)">
                                        </td>

                                        <td>
                                            <input type="number" step="0.001" name="quantity[]"
                                                class="form-control quantity" value="{{ $item->quantity }}"
                                                oninput="updateRow(this)">
                                        </td>

                                        <td>
                                            <input type="number" step="0.01" name="discount[]"
                                                class="form-control discount" value="{{ $item->discount }}"
                                                oninput="updateRow(this)">
                                        </td>

                                        <td>
                                            <select name="discount_type[]" class="form-select discount-type"
                                                onchange="updateRow(this)">
                                                <option value="flat"
                                                    {{ $item->discount_type === 'flat' ? 'selected' : '' }}>Flat</option>
                                                <option value="percent"
                                                    {{ $item->discount_type === 'percent' ? 'selected' : '' }}>%</option>
                                            </select>
                                        </td>

                                        <td>
                                            <input type="number" step="0.01" name="gst_percent[]"
                                                class="form-control gst-percent" value="{{ $item->gst_percent }}"
                                                oninput="updateRow(this)">
                                        </td>

                                        <td>
                                            <input type="text" name="gst_amount[]" class="form-control gst-amount"
                                                value="{{ $item->gst_amount }}" readonly>
                                        </td>

                                        <td>
                                            <input type="text" name="total[]" class="form-control line-total"
                                                value="{{ $item->total }}" readonly>
                                        </td>

                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="deleteRow(this)">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                    <hr class="my-4">

                    <!-- TOTAL CALC -->
                    <div class="row g-3 justify-content-end">
                        <div class="col-md-4">

                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Subtotal:</span>
                                <input type="text" name="subtotal" id="subtotal" value="{{ $purchase->subtotal }}"
                                    class="form-control form-control-sm w-50" readonly>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Total GST:</span>
                                <input type="text" name="tax_amount" id="tax_amount"
                                    value="{{ $purchase->tax_amount }}" class="form-control form-control-sm w-50"
                                    readonly>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Shipping Charges:</span>
                                <input type="number" step="0.01" name="shipping_charges" id="shipping_charges"
                                    class="form-control form-control-sm w-50"
                                    value="{{ $purchase->shipping_charges ?? 0 }}" oninput="calculateTotal()">
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Rounding:</span>
                                <input type="number" step="0.01" name="rounding" id="rounding"
                                    class="form-control form-control-sm w-50" value="{{ $purchase->rounding ?? 0 }}"
                                    oninput="calculateTotal()">
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold fs-5">Grand Total:</span>
                                <input type="text" name="total_amount" id="total_amount"
                                    value="{{ $purchase->total_amount }}"
                                    class="form-control form-control-sm w-50 fw-bold" readonly>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Paid Amount:</span>
                                <input type="number" step="0.01" name="paid_amount" id="paid_amount"
                                    class="form-control form-control-sm w-50" value="{{ $purchase->paid_amount ?? 0 }}"
                                    oninput="calculateDue()" placeholder="0">
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Due Amount:</span>
                                <input type="text" name="due_amount" id="due_amount"
                                    value="{{ $purchase->due_amount }}" class="form-control form-control-sm w-50"
                                    readonly>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Payment Method:</span>
                                <select name="payment_method" class="form-select form-select-sm w-50">
                                    <option value="">Nothing select</option>
                                    <option value="Cash" {{ $purchase->payment_method == 'Cash' ? 'selected' : '' }}>
                                        Cash
                                    </option>
                                    <option value="UPI" {{ $purchase->payment_method == 'UPI' ? 'selected' : '' }}>UPI
                                    </option>
                                    <option value="Bank" {{ $purchase->payment_method == 'Bank' ? 'selected' : '' }}>
                                        Bank
                                    </option>
                                    <option value="Card" {{ $purchase->payment_method == 'Card' ? 'selected' : '' }}>
                                        Card
                                    </option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- EXTRA -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" rows="3" class="form-control">{{ $purchase->notes }}</textarea>
                        </div>

                        @php
                            $file = $purchase->attachment;
                            $ext = $file ? strtolower(pathinfo($file, PATHINFO_EXTENSION)) : null;

                            // File icons
                            $icons = [
                                'pdf' => 'bi-file-earmark-pdf text-danger',
                                'jpg' => 'bi-file-earmark-image text-primary',
                                'jpeg' => 'bi-file-earmark-image text-primary',
                                'png' => 'bi-file-earmark-image text-primary',
                                'gif' => 'bi-file-earmark-image text-primary',
                                'doc' => 'bi-file-earmark-word text-info',
                                'docx' => 'bi-file-earmark-word text-info',
                                'xls' => 'bi-file-earmark-excel text-success',
                                'xlsx' => 'bi-file-earmark-excel text-success',
                                'zip' => 'bi-file-earmark-zip text-warning',
                                'rar' => 'bi-file-earmark-zip text-warning',
                            ];

                            $iconClass = $icons[$ext] ?? 'bi-file-earmark text-secondary';
                        @endphp

                        <div class="col-md-6">
                            <label class="form-label">Attachment (Bill)</label>
                            <input type="file" name="attachment" class="form-control mb-2">

                            @if ($file)
                                <a href="{{ asset($file) }}" target="_blank"
                                    class="btn btn-outline-primary w-100 text-start d-flex align-items-center justify-content-between">

                                    <div class="d-flex align-items-center">
                                        <i class="bi {{ $iconClass }} me-2"></i>
                                        <span>{{ basename($file) }}</span>
                                    </div>

                                    <span class="text-success">Click to Download</span>
                                </a>
                            @else
                                <div class="btn btn-outline-danger w-100 text-start">
                                    <i class="bi bi-x-circle me-2"></i>
                                    No file attached
                                </div>
                            @endif
                        </div>




                    </div>

                    <hr class="my-4">

                    <button type="submit" id="updateBtn" class="mdc-button mdc-button--unelevated px-5">
                        <i class="bi bi-check-circle me-2"></i> Update Purchase
                    </button>

                </form>

            </div>
        </div>

    </div>


    <!-- TEMPLATE ROW (hidden) -->
    <template id="itemRowTemplate">
        <tr>
            <td>
                <select name="product_id[]" class="form-select select2 product-select" onchange="productSelected(this)"
                    required>
                    <option value="">Select</option>
                    @foreach ($products as $p)
                        <option value="{{ $p->id }}" data-price="{{ $p->purchase_price }}"
                            data-gst="{{ $p->gst_percentage }}">
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>

            </td>

            <td><input type="number" step="0.01" name="unit_cost[]" class="form-control unit-cost"
                    oninput="updateRow(this)" required></td>
            <td><input type="number" step="0.001" name="quantity[]" class="form-control quantity"
                    oninput="updateRow(this)"></td>
            <td><input type="number" step="0.01" name="discount[]" class="form-control discount"
                    oninput="updateRow(this)"></td>

            <td>
                <select name="discount_type[]" class="form-select discount-type" onchange="updateRow(this)">
                    <option value="flat">Flat</option>
                    <option value="percent">%</option>
                </select>
            </td>

            <td><input type="number" step="0.01" name="gst_percent[]" class="form-control gst-percent"
                    oninput="updateRow(this)"></td>
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
        window.PURCHASE_UPDATE_ROUTE = "{{ route('purchase.update', $purchase->id) }}";
    </script>
    <script src="{{ asset('assets/backend/js/purchase.js') }}"></script>
@endpush
