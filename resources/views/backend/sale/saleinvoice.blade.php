<!DOCTYPE html>
<html>

<head>
    <title>Invoice</title>

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

        h2,
        h3,
        h4,
        h5 {
            margin: 0;
            padding: 0;
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

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        /* CLEAN PRINT MODE */
        @media print {
            body * {
                visibility: hidden;
            }

            #print-area,
            #print-area * {
                visibility: visible;
            }

            #print-area {
                position: absolute;
                inset: 0;
            }
        }
    </style>
</head>

<body>

    <div id="print-area">
        <h5 style="text-align:right;">Invoice</h5>
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
                    <p>M: {{ $company->mobile ?? '9999999999' }}, Mail: {{ $company->email ?? 'company@example.com' }}
                    </p>
                </td>

                <td class="text-right" style="width:25%">
                    <h4><strong>Bill #: {{ $sale->reference_no }}</strong></h4>
                    <p>Date: {{ formatDate($sale->sale_date) }}</p>
                    <p>Time: {{ date('h:i:s A') }}</p>
                </td>
            </tr>
        </table>

        <br>
        <!-- BILL TO / SHIP TO -->
        <table>
            <tr>
                <th>Bill To</th>
                <th>Ship To</th>
            </tr>
            <tr>
                <td>
                    <strong>{{ $sale->customer->first_name . ' ' . $sale->customer->last_name }}</strong><br>
                    {{ $sale->customer->billing_address }}<br>
                    {{ $sale->customer->cityData?->name }}, {{ $sale->customer->stateData?->name }}<br>
                    {{ $sale->customer->mobile_number }}
                </td>

                <td>
                    {{ $sale->customer->shipping_address ?? $sale->customer->billing_address }}<br>
                    {{ $sale->customer->cityData?->name }}, {{ $sale->customer->stateData?->name }}
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
                    <th>MRP</th>
                    <th>Qty</th>
                    <th>Price/Unit</th>
                    <th>Discount</th>
                    <th>GST %</th>
                    <th>GST Amt</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($sale->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->product->hsn ?? '-' }}</td>
                        <td>{{ number_format($item->mrp ?? $item->price, 2) }}</td>
                        <td>{{ number_format($item->quantity, 2) }}</td>
                        <td>{{ number_format($item->price, 2) }}</td>
                        <td>{{ number_format($item->discount, 2) }}</td>
                        <td>{{ number_format($item->tax_percent, 2) }}%</td>
                        <td>{{ number_format($item->tax_amount, 2) }}</td>
                        <td>{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <br>

        <!-- TOTALS -->
        <table>
            <tr>
                <td style="width:50%; vertical-align: top !important;">
                    <strong>Bill Amount in Words:</strong><br>
                    {{ amountInWords($sale->total_amount) }}
                </td>

                <td>
                    <table style="width:100%;">
                        <tr>
                            <td>Sub Total:</td>
                            <td class="text-right">{{ number_format($sale->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Discount:</td>
                            <td class="text-right">{{ number_format($sale->discount_amount ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Tax:</td>
                            <td class="text-right">{{ number_format($sale->tax_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Grand Total:</strong></td>
                            <td class="text-right"><strong>{{ number_format($sale->total_amount, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td>Paid:</td>
                            <td class="text-right">{{ number_format($sale->paid_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Balance:</td>
                            <td class="text-right">{{ number_format($sale->due_amount, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <br>

        <!-- TAX BREAKDOWN -->
        <table>
            <tr>
                <th>HSN</th>
                <th>Taxable Amount</th>
                <th>CGST</th>
                <th>SGST</th>
                <th>IGST</th>
                <th>Total Tax</th>
            </tr>

            <tr style="text-align: center">
                <td>{{ $sale->items->first()->product->hsn ?? '-' }}</td>
                <td>{{ number_format($sale->subtotal, 2) }}</td>
                <td>{{ number_format($sale->tax_amount / 2, 2) }}</td>
                <td>{{ number_format($sale->tax_amount / 2, 2) }}</td>
                <td>0.00</td>
                <td>{{ number_format($sale->tax_amount, 2) }}</td>
            </tr>
        </table>

        <br>

        <!-- TERMS & CONDITIONS -->
        <h4>Terms & Conditions</h4>
        <p>Goods once sold will not be taken back.</p>
        <p>Payments must be made as per terms.</p>
        <p>Company is not responsible for transport damages.</p>

        <br>

        <!-- BANK DETAILS -->
        <h4>Bank Details</h4>
        <p><strong>Bank Name:</strong> {{ $company->bank_name ?? 'State Bank of India' }}</p>
        <p><strong>Account Number:</strong> {{ $company->bank_account_number ?? '12345678901' }}</p>
        <p><strong>IFSC:</strong> {{ $company->bank_ifsc ?? 'SBIN0000000' }}</p>

        <br><br>

        <p class="text-right"><strong>Authorized Signatory</strong></p>

    </div>

</body>

</html>
