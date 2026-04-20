<!DOCTYPE html>
<html>

<head>
    <title>Purchase Invoice</title>

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
            page-break-inside: auto;
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

        /*MOST IMPORTANT: Repeat table header on every page */
        thead {
            display: table-header-group;
        }

        /*Repeat footer too (if you add one using <tfoot>) */
        tfoot {
            display: table-footer-group;
        }

        /* Prevent row from breaking across pages */
        tr {
            page-break-inside: avoid;
        }

        /* Ensure long tables flow correctly across pages */
        html,
        body {
            height: 100%;
        }
    </style>
</head>

<body>
    <div class="text-center" style="text-align: center; font-weight:bold; margin:2px;">Purchase Invoice</div>
    <div style="border: 1px solid gray;">

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
                    <p>M: {{ $company->mobile }}, Mail: {{ $company->email }}</p>
                </td>

                <td class="text-right" style="width:25%">
                    <h4>Purchase No:<br><strong> {{ $purchase->bill_no }}</strong></h4>
                    <p>Date: {{ formatDate($purchase->purchase_date) }}</p>
                    <p>Time: {{ date('h:i:s A') }}</p>
                </td>
            </tr>
        </table>

        <br>

        <!-- SUPPLIER INFO -->
        <table>
            <tr>
                <th>Supplier Details</th>
                <th>Company Receiving Goods</th>
            </tr>

            <tr>
                <td>
                    <strong>{{ $purchase->supplier->first_name . ' ' . $purchase->supplier->last_name }}</strong>
                    {{ $purchase->supplier->address }}<br>
                    {{ $purchase->supplier->cityData?->name }}, {{ $purchase->supplier->stateData?->name }}<br>
                    Mobile: {{ $purchase->supplier->mobile_number }}<br>
                    GST: {{ $purchase->supplier->gst_number ?? '-' }}
                </td>

                <td style="text-align: right;">
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
                    <th>Item</th>
                    <th>HSN</th>
                    <th>Qty</th>
                    <th>Price/Unit</th>
                    <th>Discount</th>
                    <th>GST %</th>
                    <th>GST Amount</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($purchase->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->product->hsn ?? '-' }}</td>
                        <td>{{ number_format($item->quantity, 2) }}</td>
                        <td>{{ number_format($item->unit_cost, 2) }}</td>
                        <td>{{ number_format($item->discount ?? 0, 2) }}</td>
                        <td>{{ number_format($item->gst_percent, 2) }}%</td>
                        <td>{{ number_format($item->gst_amount, 2) }}</td>
                        <td>{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <br>

        <!-- TOTALS -->
        <table>
            <tr>
                <td style="width:50%; vertical-align:top;">
                    <strong>Amount in Words:</strong><br>
                    {{ amountInWords($purchase->total_amount) }}
                </td>

                <td>
                    <table style="width:100%;">
                        <tr>
                            <td>Sub Total:</td>
                            <td class="text-right">{{ number_format($purchase->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Discount:</td>
                            <td class="text-right">{{ number_format($purchase->discount_amount ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Tax:</td>
                            <td class="text-right">{{ number_format($purchase->tax_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Grand Total:</strong></td>
                            <td class="text-right"><strong>{{ number_format($purchase->total_amount, 2) }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td>Paid:</td>
                            <td class="text-right">{{ number_format($purchase->paid_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Balance:</td>
                            <td class="text-right">{{ number_format($purchase->due_amount, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <br>

        <!-- GST SPLIT -->
        <table>
            <tr>
                <th>HSN</th>
                <th>Taxable</th>
                <th>CGST</th>
                <th>SGST</th>
                <th>IGST</th>
                <th>Total Tax</th>
            </tr>

            <tr class="text-center">
                <td>{{ $purchase->items->first()->product->hsn ?? '-' }}</td>
                <td>{{ number_format($purchase->subtotal, 2) }}</td>
                <td>{{ number_format($purchase->tax_amount / 2, 2) }}</td>
                <td>{{ number_format($purchase->tax_amount / 2, 2) }}</td>
                <td>0.00</td>
                <td>{{ number_format($purchase->tax_amount, 2) }}</td>
            </tr>
        </table>

        <br>

        <!-- TERMS -->
        <div style="padding: 3px;">
            <h4>Terms & Conditions</h4>
            <p>Goods received must be verified within 24 hours.</p>
            <p>Any discrepancy must be reported immediately.</p>
            <p>Company is not responsible for transit shortages after acknowledgment.</p>
        </div>

        <br>

        <p class="text-right"><strong>Authorized Signatory</strong></p>

    </div>

</body>

</html>
