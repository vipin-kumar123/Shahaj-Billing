<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ItemReportExport implements FromCollection, WithHeadings, WithStyles
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Invoice No',
            'Customer',
            'Item Name',
            'Brand',
            'Unit Price',
            'Qty',
            'Tax',
            'Discount',
            'Total'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [

            // Header bold
            1 => [
                'font' => ['bold' => true],
            ],

            // Invoice No column (B) → Right align
            'B' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],

            // Tax column (H) → Right align
            'H' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],

        ];
    }

    public function collection()
    {
        return $this->items->map(function ($row) {
            return [
                Carbon::parse($row->sale->sale_date)->format('d-m-Y'),
                $row->sale->invoice_no,
                ($row->sale->customer->first_name ?? '') . ' ' . ($row->sale->customer->last_name ?? ''),

                $row->product->name ?? '-',

                $row->product->brand->name ?? '-',

                number_format($row->price, 2),
                $row->quantity,

                number_format($row->tax_amount, 2),

                number_format($row->discount ?? 0, 2),

                number_format($row->subtotal, 2),
            ];
        });
    }
}
