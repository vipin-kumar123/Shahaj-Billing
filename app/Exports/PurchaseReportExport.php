<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PurchaseReportExport implements FromCollection, WithHeadings, WithStyles
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
            'Bill No',
            'Supplier',
            'Grand Total',
            'Paid Amount',
            'Balance',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header bold
            1 => [
                'font' => ['bold' => true],
            ],

            // Amount columns right align (D, E, F)
            'D' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]],
            'E' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]],
            'F' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]],
        ];
    }

    public function collection()
    {
        return collect($this->items)->map(function ($item) {
            return [
                'Date'         => Carbon::parse($item['purchase_date'])->format('d-m-Y'),
                'Bill No'      => $item['bill_no'],
                'Supplier'     => $item['supplier'],
                'Grand Total'  => $item['grand_total'],
                'Paid Amount'  => $item['paid_amount'],
                'Balance'      => $item['balance'],
            ];
        });
    }
}
