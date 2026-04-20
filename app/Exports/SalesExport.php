<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class SalesExport implements FromCollection, WithHeadings, WithStyles
{
    protected $sales;

    public function __construct($sales)
    {
        $this->sales = $sales;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Invoice No',
            'Customer',
            'Total',
            'Paid',
            'Due',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function collection()
    {
        return $this->sales->map(function ($sale) {
            return [
                'Date'       => Carbon::parse($sale->sale_date)->format('d-m-Y'),
                'Invoice_no' => $sale->invoice_no,
                'Customer'   => $sale->customer->first_name . ' ' . $sale->customer->last_name,
                'Total'      => $sale->total_amount,
                'Paid'       => $sale->paid_amount,
                'Due'        => $sale->due_amount,
            ];
        });
    }
}
