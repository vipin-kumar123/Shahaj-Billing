@extends('backend.layouts.app')
@section('title')
    Billing Software | Sales Item Reports
@endsection


@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <div class="d-flex align-items-center text-muted small flex-wrap">
                <i class="bi bi-house-door me-2"></i>
                <span>Reports</span>
                <i class="bi bi-chevron-right mx-2"></i>
                <span>Item Reports</span>
                <i class="bi bi-chevron-right mx-2"></i>
            </div>
        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="mb-3">Item Report</h6>
                <hr>
                <form method="post" action="{{ route('reports.items.submit') }}">
                    @csrf
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
                            <label class="form-label">Customer</label>
                            <select class="form-select select2" name="customer">
                                <option value="">All</option>
                                @foreach ($customers ?? [] as $c)
                                    <option value="{{ $c->id }}"
                                        {{ ($filters['customer'] ?? '') == $c->id ? 'selected' : '' }}>
                                        {{ $c->first_name }} {{ $c->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Item Name</label>
                            <select class="form-select select2" name="item_id">
                                <option value="">All</option>
                                @foreach ($products ?? [] as $item)
                                    <option value="{{ $item->id }}"
                                        {{ ($filters['item_id'] ?? '') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Brand Name</label>
                            <select class="form-select select2" name="brand_id">
                                <option value="">All</option>
                                @foreach ($brands ?? [] as $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ ($filters['brand_id'] ?? '') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
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
                                <th>Invoice/Reference No</th>
                                <th>Customer</th>
                                <th>Item Name</th>
                                <th>Brand</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Tax</th>
                                <th>Discount</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($items ?? [] as $key => $row)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->sale->sale_date)->format('d-m-Y') }}</td>

                                    <td>{{ $row->sale->invoice_no }}</td>

                                    <td>{{ $row->sale->customer->first_name ?? '' }}</td>


                                    <td>{{ $row->product->name ?? '-' }}</td>


                                    <td>{{ $row->product->brand->name ?? '-' }}</td>

                                    <td>{{ number_format($row->price, 2) }}</td>
                                    <td>{{ $row->quantity }}</td>
                                    <td>{{ number_format($row->tax_amount, 2) }}</td>
                                    <td>{{ number_format($row->discount, 2) }}</td>
                                    <td>{{ number_format($row->subtotal, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center">No Data</td>
                                </tr>
                            @endforelse
                        </tbody>

                        <tfoot>
                            <tr class="text-right">
                                <th colspan="7">Total</th>
                                <th>{{ $totals['qty'] ?? 0 }}</th>
                                <th>{{ number_format($totals['tax'] ?? 0, 2) }}</th>
                                <th>{{ number_format($totals['discount'] ?? 0, 2) }}</th>
                                <th>{{ number_format($totals['subtotal'] ?? 0, 2) }}</th>
                            </tr>
                        </tfoot>

                    </table>


                </div>

            </div>
        </div>


    </div>
@endsection


@push('footer-script')
@endpush
