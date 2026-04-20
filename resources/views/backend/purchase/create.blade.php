@extends('backend.layouts.app')
@section('title')
    Billing Software | Create Purchase
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
                <span class="fw-semibold text-dark">Create Purchase</span>
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

                <form id="purchaseForm" method="POST" enctype="multipart/form-data">
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
                                        <option value="{{ $sup->id }}">
                                            {{ $sup->first_name }} {{ $sup->last_name }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Bill Date<span class="text-danger">*</span></label>
                            <input type="date" name="purchase_date" class="form-control datepicker"
                                value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Bill No<span class="text-danger">*</span></label>
                            <input type="text" name="bill_no" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Purchase Code<span class="text-danger">*</span></label>
                            <div class="input-group">

                                <!-- PB/ Prefix -->
                                <input type="text" id="prefix" name="reference_no_prefix" class="form-control"
                                    value="PB/" disabled="disabled">

                                <!-- # symbol -->
                                <span class="input-group-text">#</span>

                                <!-- Auto Number -->
                                <input type="number" id="code_no" name="reference_no" class="form-control"
                                    placeholder="6981C3B375EAD">
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

                            </tbody>
                        </table>
                    </div>

                    <hr class="my-4">

                    <!-- TOTAL CALC -->
                    <div class="row g-3 justify-content-end">
                        <div class="col-md-4">

                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Subtotal:</span>
                                <input type="text" name="subtotal" id="subtotal"
                                    class="form-control form-control-sm w-50" readonly>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Total GST:</span>
                                <input type="text" name="tax_amount" id="tax_amount"
                                    class="form-control form-control-sm w-50" readonly>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Shipping Charges:</span>
                                <input type="number" step="0.01" name="shipping_charges" id="shipping_charges"
                                    class="form-control form-control-sm w-50" value="0" oninput="calculateTotal()">
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Rounding:</span>
                                <input type="number" step="0.01" name="rounding" id="rounding"
                                    class="form-control form-control-sm w-50" value="0" oninput="calculateTotal()">
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold fs-5">Grand Total:</span>
                                <input type="text" name="total_amount" id="total_amount"
                                    class="form-control form-control-sm w-50 fw-bold" readonly>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Paid Amount:</span>
                                <input type="number" step="0.01" name="paid_amount" id="paid_amount"
                                    class="form-control form-control-sm w-50" value="0" oninput="calculateDue()"
                                    placeholder="0">
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Due Amount:</span>
                                <input type="text" name="due_amount" id="due_amount"
                                    class="form-control form-control-sm w-50" readonly>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Payment Method:</span>
                                <select name="payment_method" class="form-select form-select-sm w-50">
                                    <option value="">Nothing select</option>
                                    <option value="Cash">Cash</option>
                                    <option value="UPI">UPI</option>
                                    <option value="Bank">Bank</option>
                                    <option value="Card">Card</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- EXTRA -->
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" rows="3" class="form-control"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Attachment (Bill)</label>
                            <input type="file" name="attachment" class="form-control">
                        </div>

                    </div>

                    <hr class="my-4">

                    <button type="submit" id="purchaseBtn" class="mdc-button mdc-button--unelevated px-5">
                        <i class="bi bi-check-circle me-2"></i> Save Purchase
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
        var aiData = @json($aiSuggestions, JSON_FORCE_OBJECT);
    </script>
    <script>
        window.PURCHASE_STORE_ROUTE = "{{ route('purchase.store') }}";
    </script>
    <script src="{{ asset('assets/backend/js/purchase.js') }}"></script>
@endpush
