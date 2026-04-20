@extends('backend.layouts.app')
@section('title')
    Billing Software | Purchase Payment Report
@endsection


@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Reports</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>Purchase Payment Report</span>
                <i class="bi bi-chevron-right mx-2"></i>
            </div>
        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="post" action="{{ route('reports.purchase.payment.submit') }}">
                    @csrf
                    <h6 class="mb-3">Purchase Payment Report</h6>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">From Date</label>
                            <input type="text" name="from_date" class="form-control datepicker"
                                value="{{ request('from_date', date('d-m-Y')) }}">
                            @error('from_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">To Date</label>
                            <input type="text" name="to_date" class="form-control datepicker"
                                value="{{ request('to_date', date('d-m-Y')) }}">
                            @error('to_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Supplier</label>
                            <select name="supplier_id" class="form-select select2">
                                <option value="">Select Supplier</option>
                                @foreach ($suppliers ?? [] as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->first_name }} {{ $supplier->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Payment Type</label>
                            <select name="payment_type" class="form-select select2">
                                <option value="">Select Payment</option>
                                <option value="Cash">Cash</option>
                                <option value="UPI">UPI</option>
                                <option value="Bank">Bank</option>
                                <option value="Card">Card</option>
                            </select>
                        </div>

                    </div>

                    <div class="mt-4">
                        <button type="submit" name="action" value="view" class="mdc-button mdc-button--unelevated px-5">
                            Submit View
                        </button>

                        <button type="submit" name="action" value="export" class="mdc-button mdc-button--success px-5">
                            Export
                        </button>
                    </div>
                </form>
            </div>
        </div>


        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Records</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Bill No / Reference No</th>
                                <th>Supplier</th>
                                <th>Payment Type</th>
                                <th class="text-end">Paid Amount</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($items ?? [] as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item['date'] }}</td>
                                    <td>{{ $item['bill_no'] }}</td>
                                    <td>{{ $item['supplier'] }}</td>
                                    <td>{{ $item['payment_type'] }}</td>
                                    <td class="text-end">₹ {{ number_format($item['paid_amount'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        No Records Found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        @if (!empty($items) && count($items) > 0)
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="5" class="text-end">Total</td>
                                    <td class="text-end">
                                        ₹ {{ number_format($total_paid ?? 0, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif

                    </table>
                </div>

            </div>
        </div>


    </div>
@endsection


@push('footer-script')
@endpush
