<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SalePaymentExport implements FromCollection, WithHeadings, WithStyles
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
            'Invoice',
            'Customer',
            'Payment Type',
            'Paid Amount'
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
                Carbon::parse($row->sale_date)->format('d-m-Y'),

                $row->invoice_no,

                ($row->customer->first_name ?? '') . ' ' . ($row->customer->last_name ?? ''),

                $row->payment_method,

                number_format($row->paid_amount, 2),
            ];
        });
    }
}
