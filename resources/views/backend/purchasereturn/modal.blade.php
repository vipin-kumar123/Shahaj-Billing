<link rel="stylesheet" href="{{ asset('assets/backend/css/demo/custom.css') }}">

{{-- make payment modal --}}
<div class="modal fade pop-out" id="receivePaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-sm">

            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Make Payment (Refund)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="makeReturnPaymentForm" method="post">
                @csrf
                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label">Supplier</label>
                                <input type="text" id="mp_supplier" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label">Return No</label>
                                <input type="text" id="mp_bill_no" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label">Return Total Amount</label>
                                <input type="text" id="mp_total" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label">Refund Due</label>
                                <input type="text" id="mp_due" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label">Refund Amount</label>
                                <input type="hidden" id="purchase_return_id" name="purchase_return_id">
                                <input type="number" name="amount" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label">Payment Method</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="">Select</option>
                                    <option value="Cash">Cash</option>
                                    <option value="UPI">UPI</option>
                                    <option value="Bank">Bank Transfer</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label">Payment Date</label>
                                <input type="text" name="payment_date" class="form-control datepicker"
                                    placeholder="yy-mm-d" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label">Note</label>
                                <textarea name="note" class="form-control"></textarea>
                            </div>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

                        <button type="submit" id="returnPaymentBtn" class="btn btn-primary px-4">
                            <span class="btn-text">Submit</span>
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>



{{-- payment history modal  --}}
<div class="modal fade pop-out" id="paymentHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-sm">

            <!-- HEADER -->
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Payment History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->

            <div class="modal-body">

                <!-- Top Summary Section -->
                <div class="row mb-3">

                    <!-- Payment Details -->
                    <div class="col-md-6">
                        <h6 class="fw-bold">Payment Details</h6>
                        <p class="mb-1">Supplier / Party Name: <span id="ph_supplier"></span></p>
                        <p class="mb-1">Purchase Bill Code: <span id="ph_bill_code"></span></p>
                        <p class="mb-1">Date: <span id="ph_bill_date"></span></p>
                    </div>

                    <!-- Payment Summary -->
                    <div class="col-md-6">
                        <h6 class="fw-bold">Payment Summary</h6>
                        <p class="mb-1">Paid Amount: ₹<span id="ph_paid_amount"></span></p>
                        <p class="mb-1">Balance: ₹<span id="ph_balance"></span></p>
                    </div>

                </div>

                <table id="returnPaymentTable" class="table table-bordered w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start" style="width: 25%;">Transaction Date</th>
                            <th class="text-center" style="width: 25%;">Payment Type</th>
                            <th class="text-center" style="width: 25%;">Amount</th>
                            <th class="text-center" style="width: 25%;">Action</th>
                        </tr>
                    </thead>

                    <tbody id="ph_table_body">
                        <!-- Dynamic Rows -->
                    </tbody>

                    <tfoot>
                        <tr>
                            <!-- 2 columns ko merge kar ke Right-align -->
                            <th colspan="2" class="text-end">Total Amount:</th>

                            <!-- 4th column me total -->
                            <th id="ph_total_amount" class="text-center">0</th>
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
