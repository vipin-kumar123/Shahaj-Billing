<!DOCTYPE html>
<html>

<head>
    <title>Purchase Return Invoice</title>

    <style>
        @page {
            size: A4;
            margin: 12mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 5px;
        }

        .no-border td {
            border: none !important;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <div class="text-center">Purchase Return Invoice</div>
    <div id="print-area" style="border: 1px solid gray; margin:2px;">
        <!-- HEADER -->
        <table class="no-border">
            <tr>
                <td style="width: 25%">
                    @if ($logo)
                        <img src="data:image/png;base64,{{ $logo }}" style="max-width:120px;">
                    @else
                        <img src="https://via.placeholder.com/120x70?text=Logo">
                    @endif
                </td>

                <td class="text-center">
                    <h2>{{ $company->name }}</h2>
                    <p>{{ $company->address }}</p>
                    <p>{{ $company->city?->name }}, {{ $company->state?->name }}</p>
                    <p>M: {{ $company->mobile }} | Email: {{ $company->email }}</p>
                </td>

                <td class="text-right" style="width: 25%">
                    <h4><strong>Return No: {{ $purchaseReturn->return_no }}</strong></h4>
                    <p>Date: {{ formatDate($purchaseReturn->return_date) }}</p>
                    <p>Against Bill: {{ $purchaseReturn->purchase->bill_no }}</p>
                </td>
            </tr>
        </table>

        <br>
        <!-- SUPPLIER INFO -->
        <table>
            <tr>
                <th>Supplier Details</th>
                <th>Company (Receiving Return)</th>
            </tr>

            <tr>
                <td>
                    <strong>{{ $purchaseReturn->supplier->first_name }}
                        {{ $purchaseReturn->supplier->last_name }}</strong><br>
                    {{ $purchaseReturn->supplier->billing_address }}<br>
                    {{ $purchaseReturn->supplier->cityData?->name }},
                    {{ $purchaseReturn->supplier->stateData?->name }}<br>
                    Mobile: {{ $purchaseReturn->supplier->mobile_number }}<br>
                    GST: {{ $purchaseReturn->supplier->gst_number ?? '-' }}
                </td>

                <td class="text-right">
                    <strong>{{ $company->name }}</strong><br>
                    {{ $company->address }}<br>
                    {{ $company->city?->name }}, {{ $company->state?->name }}<br>
                    Mobile: {{ $company->mobile }}
                </td>
            </tr>
        </table>

        <br>

        <!-- ITEMS TABLE -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Return Qty</th>
                    <th>Unit Cost</th>
                    <th>GST %</th>
                    <th>GST Amount</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @php $totalGst = 0; @endphp
                @foreach ($purchaseReturn->items as $index => $item)
                    @php $totalGst += $item->gst_amount; @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ number_format($item->return_qty, 2) }}</td>
                        <td>{{ number_format($item->unit_cost, 2) }}</td>
                        <td>{{ number_format($item->gst_percent, 2) }}%</td>
                        <td>{{ number_format($item->gst_amount, 2) }}</td>
                        <td>{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <br>

        <!-- TOTAL SECTION -->
        <table>
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <strong>Amount in Words:</strong><br>
                    {{ amountInWords($purchaseReturn->total_return_amount) }}
                </td>

                <td>
                    <table style="width: 100%;">
                        <tr>
                            <td>Total GST:</td>
                            <td class="text-right">{{ number_format($totalGst, 2) }}</td>
                        </tr>

                        <tr>
                            <td><strong>Total Return Amount:</strong></td>
                            <td class="text-right">
                                <strong>{{ number_format($purchaseReturn->total_return_amount, 2) }}</strong>
                            </td>
                        </tr>

                        <tr>
                            <td>Refunded:</td>
                            <td class="text-right">{{ number_format($purchaseReturn->refunded_amount, 2) }}</td>
                        </tr>

                        <tr>
                            <td><strong>Refund Due:</strong></td>
                            <td class="text-right text-primary">
                                <strong>{{ number_format($purchaseReturn->refund_due, 2) }}</strong>
                            </td>
                        </tr>

                        <tr>
                            <td>Status:</td>
                            <td class="text-right">
                                {{ $purchaseReturn->refund_status }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <br>

        <!-- TERMS -->
        <div style="padding: 2px;">
            <h4>Terms & Conditions</h4>
            <p>Returned goods must be verified within 24 hours.</p>
            <p>Any issue must be reported immediately.</p>
            <p>Company is not responsible for transit-related issues after handover.</p>
        </div>


        <br>

        <p class="text-right"><strong>Authorized Signatory</strong></p>

    </div>

</body>

</html>
