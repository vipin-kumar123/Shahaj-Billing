<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiTableExport implements WithMultipleSheets
{
    protected $tables;

    public function __construct($tables)
    {
        $this->tables = $tables;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->tables as $name => $data) {
            $sheets[] = new SingleTableSheet($data, $name);
        }

        return $sheets;
    }
}
