<!DOCTYPE html>
<html>

<head>
    <title>Sale Return Invoice</title>

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

        .no-border td,
        .no-border th {
            border: none !important;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>

    <div id="print-area">

        <h5 style="text-align:right;">Sale Return</h5>

        <!-- HEADER -->
        <table class="no-border">
            <tr>
                <td style="width:20%">
                    @if ($logo)
                        <img src="data:image/png;base64,{{ $logo }}" style="max-width:120px;">
                    @else
                        <img src="https://via.placeholder.com/120x70?text=Logo">
                    @endif
                </td>

                <td class="text-center">
                    <h2>{{ $company->name }}</h2>
                    <p>{{ $company->city?->name }}, {{ $company->state?->name }}</p>
                    <p>M: {{ $company->mobile }}, Email: {{ $company->email }}</p>
                </td>

                <td class="text-right" style="width:25%">
                    <h4><strong>Return No: {{ $saleReturn->return_no }}</strong></h4>
                    <p>Date: {{ formatDate($saleReturn->return_date) }}</p>
                    <p>Against Invoice: {{ $saleReturn->sale->invoice_no }}</p>
                </td>
            </tr>
        </table>

        <br>

        <!-- CUSTOMER -->
        <table>
            <tr>
                <th>Customer</th>
                <th>Original Invoice</th>
            </tr>
            <tr>
                <td>
                    <strong>{{ $saleReturn->customer->first_name }} {{ $saleReturn->customer->last_name }}</strong><br>
                    {{ $saleReturn->customer->billing_address }}<br>
                    {{ $saleReturn->customer->cityData?->name }},
                    {{ $saleReturn->customer->stateData?->name }}<br>
                    Mobile: {{ $saleReturn->customer->mobile_number }}<br>
                    GST: {{ $saleReturn->customer->gst_number ?? '-' }}
                </td>

                <td>
                    Invoice No: {{ $saleReturn->sale->invoice_no }}<br>
                    Invoice Date: {{ formatDate($saleReturn->sale->sale_date) }}
                </td>
            </tr>
        </table>

        <br>

        <!-- ITEMS TABLE -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>HSN</th>
                    <th>Returned Qty</th>
                    <th>Unit Price</th>
                    <th>GST %</th>
                    <th>GST Amt</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @php $totalGst = 0; @endphp
                @foreach ($saleReturn->items as $i => $item)
                    @php $totalGst += $item->gst_amount; @endphp

                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->product->hsn ?? '-' }}</td>

                        <td class="text-right">{{ rtrim(rtrim($item->return_qty, '0'), '.') }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>

                        <td class="text-right">{{ number_format($item->gst_percent, 2) }}%</td>
                        <td class="text-right">{{ number_format($item->gst_amount, 2) }}</td>
                        <td class="text-right">{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <br>

        <!-- TOTALS -->
        <table>
            <tr>
                <td style="width:50%; vertical-align:top;">
                    <strong>Return Amount in Words:</strong><br>
                    {{ amountInWords($saleReturn->total_return_amount) }}
                </td>

                <td>
                    <table style="width:100%;">
                        <tr>
                            <td>Total GST:</td>
                            <td class="text-right">{{ number_format($totalGst, 2) }}</td>
                        </tr>

                        <tr>
                            <td><strong>Total Return Amount:</strong></td>
                            <td class="text-right">
                                <strong>{{ number_format($saleReturn->total_return_amount, 2) }}</strong>
                            </td>
                        </tr>

                        <tr>
                            <td>Refunded:</td>
                            <td class="text-right">{{ number_format($saleReturn->refunded_amount ?? 0, 2) }}</td>
                        </tr>

                        <tr>
                            <td><strong>Refund Due:</strong></td>
                            <td class="text-right text-primary">
                                <strong>{{ number_format($saleReturn->refund_due ?? 0, 2) }}</strong>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <br>

        @if ($saleReturn->note)
            <p><strong>Note:</strong> {{ $saleReturn->note }}</p>
        @endif

        <br><br>

        <p class="text-right"><strong>Authorized Signatory</strong></p>

    </div>

</body>

</html>
