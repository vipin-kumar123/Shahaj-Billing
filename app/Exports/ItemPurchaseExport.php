<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ItemPurchaseExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
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
            'Item',
            'Brand',
            'Unit Price',
            'Quantity',
            'Discount',
            'Tax',
            'Total',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header bold
            1 => [
                'font' => ['bold' => true],
            ],

            // Amount columns align right
            'F' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]],
            'G' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]],
            'H' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]],
            'I' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]],
            'J' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]],
        ];
    }

    public function collection()
    {
        return collect($this->items)->map(function ($item) {

            return [
                'Date'        => $item['date'],
                'Bill No'     => $item['bill_no'] ?? '',
                'Supplier'    => $item['supplier'] ?? '',
                'Item'        => $item['item'] ?? '',
                'Brand'       => $item['brand'] ?? '',

                'Unit Price'  => (float) ($item['unit_price'] ?? 0),
                'Quantity'    => (int)   ($item['quantity'] ?? 0),
                'Discount'    => (float) ($item['discount'] ?? 0),
                'Tax'         => (float) ($item['tax'] ?? 0),
                'Total'       => (float) ($item['total'] ?? 0),
            ];
        });
    }
}
