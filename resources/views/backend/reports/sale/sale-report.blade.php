@extends('backend.layouts.app')
@section('title')
    Billing Software | Sales Reports
@endsection


@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Reports</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>Sales Reports</span>
                <i class="bi bi-chevron-right mx-2"></i>
            </div>
        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="mb-3">Sale Report</h6>
                <hr>
                <form method="post" action="{{ route('reports.sales.submit') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">From Date*</label>
                            <input type="text" name="from_date" class="form-control datepicker"
                                value="{{ request('from_date', date('d-m-Y')) }}">
                            @error('from_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">To Date*</label>
                            <input type="text" name="to_date" class="form-control datepicker"
                                value="{{ request('to_date', date('d-m-Y')) }}">
                            @error('to_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Customer</label>
                            <select class="form-select select2" name="customer[]" data-placeholder="Select Customer"
                                multiple>
                                @foreach ($customers as $c)
                                    <option value="{{ $c->id }}"
                                        {{ in_array($c->id, $filters['customer'] ?? []) ? 'selected' : '' }}>
                                        {{ $c->first_name }} {{ $c->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
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


        @if (!empty($filters))
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Records</h5>

                        {{-- <div>
                            <button id="exportSale" class="mdc-button mdc-button--success px-5">
                                Export
                            </button>
                        </div> --}}
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered mt-3">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Invoice</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Due</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($sales as $key => $sale)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ date('d-m-Y', strtotime($sale->sale_date)) }}</td>
                                        <td>{{ $sale->invoice_no }}</td>
                                        <td>
                                            {{ $sale->customer->first_name ?? '' }}
                                            {{ $sale->customer->last_name ?? '' }}
                                        </td>
                                        <td>₹{{ number_format($sale->total_amount, 2) }}</td>
                                        <td>₹{{ number_format($sale->paid_amount, 2) }}</td>
                                        <td>₹{{ number_format($sale->due_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            No Records Found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        @endif

    </div>
@endsection


@push('footer-script')
@endpush
