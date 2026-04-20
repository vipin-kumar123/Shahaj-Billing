<link rel="stylesheet" href="{{ asset('assets/backend/css/demo/custom.css') }}">

{{-- make payment modal  --}}
<div class="modal fade pop-out" id="refund-payment-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-sm">

            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Make Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="refundPaymentForm" method="post">
                @csrf
                <div class="modal-body">

                    <input type="hidden" name="sale_return_id" id="rf_id">

                    <div class="row">

                        <!-- Customer -->
                        <div class="col-md-6">
                            <label class="form-label">Customer</label>
                            <input type="text" id="rf_customer" class="form-control" readonly>
                        </div>

                        <!-- Return No -->
                        <div class="col-md-6">
                            <label class="form-label">Return No</label>
                            <input type="text" id="rf_return_no" class="form-control" readonly>
                        </div>

                        <!-- Total Return Amount -->
                        <div class="col-md-6">
                            <label class="form-label">Return Total</label>
                            <input type="text" id="rf_return_total" class="form-control" readonly>
                        </div>

                        <!-- Refund Due -->
                        <div class="col-md-6">
                            <label class="form-label">Refund Due</label>
                            <input type="text" id="rf_refund_due" class="form-control" readonly>
                        </div>

                        <!-- Refund Amount -->
                        <div class="col-md-6">
                            <label class="form-label">Refund Amount</label>
                            <input type="number" name="amount" class="form-control" required>
                        </div>

                        <!-- Refund Method -->
                        <div class="col-md-6">
                            <label class="form-label">Refund Method</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="">Select</option>
                                <option value="Cash">Cash</option>
                                <option value="UPI">UPI</option>
                                <option value="Bank">Bank Transfer</option>
                                <option value="Cheque">Cheque</option>
                            </select>
                        </div>

                        <!-- Refund Date -->
                        <div class="col-md-6">
                            <label class="form-label">Refund Date</label>
                            <input type="text" name="payment_date" class="form-control datepicker"
                                placeholder="dd/mm/dd" required>
                        </div>

                        <!-- Note -->
                        <div class="col-md-6">
                            <label class="form-label">Note</label>
                            <textarea name="note" class="form-control"></textarea>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="mdc-button mdc-button--danger px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="refundPaymentBtn" class="mdc-button mdc-button--unelevated px-4">
                            <span class="btn-text">Submit</span>
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>


{{-- payment history modal  --}}
<div class="modal fade pop-out" id="history-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-sm">

            <!-- HEADER -->
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Payment Refund History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->

            <div class="modal-body">

                <!-- Top Summary Section -->
                <div class="row mb-3">

                    <!-- Payment Details -->
                    <div class="col-md-6">
                        <h6 class="fw-bold">Payment Details</h6>
                        <p class="mb-1">Customer / Party Name: <span id="rf_customer"></span></p>
                        <p class="mb-1">Sale Invoice No: <span id="rf_invoice_no"></span></p>
                        <p class="mb-1">Date: <span id="rf_sale_date"></span></p>
                    </div>

                    <!-- Payment Summary -->
                    <div class="col-md-6">
                        <h6 class="fw-bold">Payment Summary</h6>
                        <p class="mb-1">Paid Amount: ₹<span id="rf_paid_amount"></span></p>
                        <p class="mb-1">Balance: ₹<span id="rf_balance"></span></p>
                    </div>

                </div>

                <table class="table table-bordered w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start" style="width: 25%;">Transaction Date</th>
                            <th class="text-center" style="width: 25%;">Payment Type</th>
                            <th class="text-center" style="width: 25%;">Amount</th>
                            <th class="text-center" style="width: 25%;">Action</th>
                        </tr>
                    </thead>

                    <tbody id="rf_table_body">
                        <!-- Dynamic Rows -->
                    </tbody>

                    <tfoot>
                        <tr>
                            <!-- 2 columns ko merge kar ke Right-align -->
                            <th colspan="2" class="text-end">Total Amount:</th>

                            <!-- 4th column me total -->
                            <th id="rf_total_amount" class="text-center">0</th>
                        </tr>
                    </tfoot>
                </table>



                <!-- FOOTER BUTTONS -->
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>
