<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PurchasePaymentExport implements FromCollection, WithHeadings, WithStyles
{
    protected $purchase;

    public function __construct($purchase)
    {
        $this->purchase = $purchase;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Bill No',
            'Supplier',
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
            'E' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],

            // Tax column (H) → Right align
            // 'H' => [
            //     'alignment' => [
            //         'horizontal' => Alignment::HORIZONTAL_RIGHT,
            //     ],
            // ],

        ];
    }

    public function collection()
    {
        return collect($this->purchase)->map(function ($row) {
            return [
                $row['date'],
                $row['bill_no'],
                $row['supplier'],
                $row['payment_type'],
                number_format($row['paid_amount'], 2),
            ];
        });
    }
}
